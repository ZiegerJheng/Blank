<?php
class Controller
{
    private $do = null;

    private $modelName;
    private $modelResult = array();

    private $mvcGeneralConfig;

    private $blankConfigs = array();

    public function __construct()
    {
        $this->mvcGeneralConfig = parse_ini_file(CONFIG_BASE_PATH . 'mvc-general.ini', true);
    }

    private function loadBlankConfigs()
    {
        $allRawBlankConfigs = parse_ini_file(BLANKFILE_BASE_PATH . 'blank.ini', true);
        if(true === array_key_exists('doName', $allRawBlankConfigs)){
            $thisRawBlankConfigs = $allRawBlankConfigs['doName'];
        }
        else{
            $thisRawBlankConfigs = $rawBlankConfigs['default'];
        }

        foreach($thisRawBlankConfigs as $key => $value){
            $configLevels = explode('.', $key);
            $numOfConfigLevels = count($configLevels);
            $pointer =& $this->blankConfigs;
            for($index = 0; $index < $numOfConfigLevels; $index++){
                if(false == array_key_exists($configLevels[$index], $pointer)){
                    $pointer[$configLevels[$index]] = array();
                }
                $pointer =& $pointer[$configLevels[$index]];

                if(($index + 1) === $numOfConfigLevels){
                    $pointer = $value;
                }
            }
        }
    }

    private function setModelName()
    {
        $do = $_GET['do'];
        if($do !== false){
            $this->do = $do;
        }

        if(null === $this->do){
            $this->modelName = $this->modelConfig['default']['model_name'];
        }
        else{
            $this->modelName = $this->modelConfig[$this->do]['model_name'];
        }
    }

    public function run()
    {
        $this->setModelName();

        try{
            $model = $this->loadModel();

            $model->setInput(array(
                'name' => '_do_',
                'value' => $this->do
            ));

            if($model->check()){
                $model->run();
            }

            $this->modelResult = $model->loadResult();
            $this->loadView();
        }
        catch(dbException $e){
            die($e->getMessage());
        }
    }

    private function loadModel()
    {
        $modelBasePath = $this->mvcGeneralConfig['general']['model_path'];
        $modelLibPath = $modelBasePath . $this->modelName . '.class.inc.php';
        if(file_exists($modelLibPath)){
            require_once($modelLibPath);
        }

        if(true == class_exists($this->modelName)){
            return new $this->modelName();
        }

    }

    private function loadView()
    {
        $viewBasePath = $this->mvcGeneralConfig['general']['view_path'];

        foreach($this->modelResult['data'] as $vName => $vValue){
            $$vName = $vValue;
        }

        require_once($viewBasePath . $this->modelResult['view'] . '.php');
    }
}
?>
