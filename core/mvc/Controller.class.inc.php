<?php
class Controller
{
    private static $do = null;
    private static $blankConfigs = array();

    public static function run()
    {
        try{
            self::getDoName();
            self::loadBlankConfigs();
            self::doAuth();
            
            $model = self::loadModel();
            $toView = $model::run();
            
            self::loadView($toView);
        }
        catch(Exception $e){
            die($e->getMessage());
        }
    }

    private static function getDoName()
    {
    }

    private static function loadBlankConfigs()
    {
        $allRawBlankConfigs = parse_ini_file(BLANKFILE_BASE_PATH . 'blank.ini', true);
        if(true === array_key_exists(self::$do, $allRawBlankConfigs)){
            $thisRawBlankConfigs = $allRawBlankConfigs[self::$do];
        }
        else{
            $thisRawBlankConfigs = $rawBlankConfigs['default'];
        }

        foreach($thisRawBlankConfigs as $key => $value){
            $configLevels = explode('.', $key);
            $numOfConfigLevels = count($configLevels);
            $pointer =& self::$blankConfigs;
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

    private static function loadView($toView)
    {
    }
}
?>
