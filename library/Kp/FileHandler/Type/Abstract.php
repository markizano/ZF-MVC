<?php

/**
 * Use this class to extend a Kp_FileHandler_Type class.
 * Handles core functionality of loading the files URI/FilePath
 *
 * @category Ic
 * @package  Kp_FileHandler
 * @subpackage Kp_FileHandler_Type
 * @author James Solomon <james@integraclick.com>
 */
abstract class Kp_FileHandler_Type_Abstract
{
    /**
     * Environment sensitive array that sets up all needed templates to
     * find and load a file.  Settings in Production are inherited
     * into all sub-environments.
     *
     * Note: Needs to be overloaded in the extending class.
     *
     * @var array
     */
    public $templates = array();

    /**
     * Where we store the combined settings for the current environment
     *
     * @var array
     */
    protected $_settings = array();

    /**
     * The RegEx pattern to find all the template variables
     *
     * @var string
     */
    protected $_templateVariablePattern = '/\{\{(\w+)}}/';

    /**
     * The array of options used to hold template variables
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Current environment
     *
     * @var string
     */
    protected $_environment = 'development';

    /**
     * Intialize all options
     *
     * @param mixed $options Can be array or Zend_Config
     */
    public function __construct($options = array())
    {
        if (!empty($options)) {
            if (!isset($options['appRoot'])) {
                $options['appRoot'] = defined('AP') ? AP : dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/application';
            }

            if (!isset($options['environment'])) {
                $options['environment'] = defined('AE') ? AE : 'development';
            }

            $this->setOptions($options);
        }
    }

    /**
     * A generic setter for all options
     *
     * @param mixed $options Can be array or Zend_Config
     * @return Kp_FileHandler_Type_Abstract
     */
    public function setOptions($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (!is_array($options)) {
            throw new Kp_FileHandler_Exception('The param $options needs to be an array');
        }

        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }

        return $this;
    }

    /**
     * Will set the options using the setter,
     * or just put it in the options array
     *
     * @param string $name  Setting name
     * @param mixed  $value Setting value
     * @return Kp_FileHandler_Type_Abstract
     */
    public function setOption($name, $value)
    {
        $method = 'set' . ucfirst($name);
        if (is_callable(array($this, $method))) {
            $this->$method($value);
        } else {
            $this->_options[$name] = $value;
        }

        return $this;
    }

    /**
     * Retrieve option; return $default if option not set.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return (isset($this->_options[$name])) ? $this->_options[$name] : $default;
    }

    /**
     * Getter for the environment variable
     *
     * @return string Current environment
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Setter for environment variable
     *
     * @param string $environment
     * @return Kp_FileHandler_Type_Abstract
     */
    public function setEnvironment($environment)
    {
        $this->_environment = $environment;
        return $this;
    }

    /**
     * This will take all provided settings and the templates
     * and build the URI for loading the file
     *
     * @return string Generated URI
     */
    public function buildUri($checkPath = true)
    {
        if (!$checkPath) {
            return $this->_build('uri');
        }
        try {
            $filePath = $this->_build('filePath');
            if (file_exists($filePath)) {
                $fileUri = $this->_build('uri');

                if (isset($this->_settings['timestamp']) && $this->_settings['timestamp'] === true) {
                    $fileUri .= "?ts=" . ((int) microtime(true));
                }
            } else {
                throw new Kp_FileHandler_Exception("File does not exist.");
            }
        } catch (Kp_FileHandler_Exception $e) {
            $fileUri = $this->buildDefault('defaultUri');
        }

        return $fileUri;
    }

    /**
     * This will take all provided settings and the templates
     * and build the file path
     *
     * @return string Generated file path
     */
    public function buildPath()
    {
        try {
            $filePath = $this->_build('filePath');
            if (file_exists($filePath)) {
                return $filePath;
            } else {
                throw new Kp_FileHandler_Exception("File does not exist.");
            }
        } catch (Kp_FileHandler_Exception $e) {}

        return $this->buildDefault('defaultPath');
    }

    /**
     * sets a path to save a new file to
     *
     * @return string Generated file path
     */
    public function getSavePath()
    {
      return $this->_build('filePath');
    }

    /**
     * saves a file, via file_get_contents, as opposed to an uploaded file, etc (should add in something for that too)
     *
     * @return string Generated file path
    */
    public function saveFileContents($contents)
    {
        try {
            $savePath = $this->getSavePath();
            $pathInfo = pathinfo($savePath);

            if (!is_dir($pathInfo['dirname'])) {
                mkdir($pathInfo['dirname'], 0777, true);
            }

            $new = fopen($savePath, 'w+');
            fwrite($new, $contents);
            fclose($new);
        } catch (Kp_FileHandler_Exception $e) {
            return false;
        }
    }

    /**
     * get data from external img (like google chart)
     *
     * @return string Generated file path
     */
    public function getFileContents($url)
    {
        try {
            return file_get_contents($url);
        } catch (Kp_FileHandler_Exception $e) {}
    }
    /**
     * Get the current setting requested
     *
     * @param string $name Setting to load
     * @return mixed string|array|null
     */
    public function getSetting($name)
    {
        if (empty($this->_settings)) {
            $this->_buildSettings();
        }

        if (isset($this->_settings[$name])) {
            return $this->_settings[$name];
        }

        return null;
    }

    /**
     * Get all the current settings
     *
     * @return array
     */
    public function getSettings()
    {
        if (empty($this->_settings)) {
            $this->_buildSettings();
        }

        return $this->_settings;
    }

    /**
     * Will handle how to load a default file
     *
     * @param unknown_type $kind
     */
    public function buildDefault($kind)
    {
        $this->setOption('fileExtension', $this->getSetting('defaultExtension'));

        return $this->_build($kind, true);
    }

    /**
     * Build the path using the templates in the settings array
     *
     * @param string  $kind
     * @param boolean $default To use default settings
     * @return string Generated path
     */
    protected function _build($kind, $default = false)
    {
        $pathTemplate = $this->getSetting($kind);
        if (is_null($pathTemplate))
            return null;

        $fileVariable = ($default) ? 'defaultFile' : 'fileName';
        $fileTemplate = $this->getSetting($fileVariable);

        return $this->_processTemplate($pathTemplate, $fileTemplate);
    }

    /**
     * Takes the settings array and builds the current settings based on
     * environment variables.
     *
     * Note: Anything that is not 'production',
     *       will inherit 'production' settings
     *
     * @return void
     */
    protected function _buildSettings()
    {
        $settings = $this->templates[$this->_environment];

        //This is the production inheritance portion
        if ($this->_environment !== 'production') {
            $settings = array_merge($this->templates['production'], $settings);
        }

        $this->_settings = $settings;
    }

    /**
     * Takes the provided templates and will generate
     * the path for us to use.
     *
     * @param mixed $pathTemplate
     * @param mixed $fileTemplate
     * @return string Generated path
     */
    protected function _processTemplate($pathTemplate, $fileTemplate)
    {
        if (is_null($pathTemplate))
            return null;

        //Not ideal, but only thing i could think of at the time
        if (is_array($pathTemplate)) {
            $pathTemplate = $pathTemplate[array_rand($pathTemplate)];
        }

        //Not ideal, but only thing i could think of at the time
        if (is_array($fileTemplate)) {
            $fileTemplate = $fileTemplate[array_rand($fileTemplate)];
        }

        //Combine the 2 templates to be one big one
        $fullTemplate = $pathTemplate . '/' . $fileTemplate;

        //Grab the variables in the template
        $templateVariables = $this->_getTemplateVariables($fullTemplate);

        //Find and replace each variable in the template
        foreach ($templateVariables as $templateVariable) {
            $tempVar = '{{' . $templateVariable . '}}';
            $tempVal = $this->_options[$templateVariable];
            $fullTemplate = str_replace($tempVar, $tempVal, $fullTemplate);
        }

        return $fullTemplate;
    }

    protected function _buildPath($pathSetting, $fileSetting)
    {
        $path = $this->getSetting($pathSetting);
        $file = $this->getSetting($fileSetting);

        if (is_null($path))
            return null;

        return $this->_processTemplate($path, $file);
    }

    /**
     * Handles finding all the variables in the template
     *
     * @param string $template
     * @return mixed
     */
    protected function _getTemplateVariables($template)
    {
        $pregMatch = preg_match_all($this->_templateVariablePattern, $template, $variables);

        /**
         * If no matches are in the template, thats ok.
         *
         * Why Ok? B/c if someone has static files referenced, this will
         *         still be able to load them
         */
        if (!$pregMatch) {
            return array();
        }

        $variables = $variables[1];

        //Make sure we have all the needed variables
        $this->_checkForVariables($variables);

        return $variables;
    }

    /**
     * Verifies that all the tempalate variables are set in our
     * options array
     *
     * @throws Kp_FileHandler_Exception
     * @param array $variables
     * @return boolean
     */
    private function _checkForVariables(array $variables)
    {
        foreach ($variables as $variable) {
            if (!isset($this->_options[$variable])) {
                throw new Kp_FileHandler_Exception("The option '{$variable}' is needed but not supplied");
            }
        }

        return true;
    }
}
