<?php
/**
* @package     
* @subpackage  
* @author      Brice Tencé
* @copyright   2012 Brice Tencé
* @link        
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

/**
* plugin for jResponseHTML, which pre-processes CSS files (e.g. Stylus, Less, Sass) using sub-plugins
*/

require_once( 'ICSSpreproPlugin.class.php' );

define('CSSPREPRO_COMPILE_ALWAYS', 1 );
define('CSSPREPRO_COMPILE_ONCHANGE', 2 ); //default value : CSSprepro default behaviour
define('CSSPREPRO_COMPILE_ONCE', 3 );

class CSSpreproHTMLResponsePlugin implements jIHTMLResponsePlugin {

    protected $response = null;

    public function __construct(jResponse $c) {
        $this->response = $c;
    }

    /**
     * called just before the jResponseBasicHtml::doAfterActions() call
     */
    public function afterAction() {
    }

    /**
     * called when the content is generated, and potentially sent, except
     * the body end tag and the html end tags. This method can output
     * directly some contents.
     */
    public function beforeOutput() {
        if (!($this->response instanceof jResponseHtml))
            return;
        global $gJConfig;

        $defaultCompileFlag = CSSPREPRO_COMPILE_ONCHANGE;
        if( isset($gJConfig->jResponseHtml['CSSprepro_compile']) ) {
            $configCompileFlag = $this->translateCompileFlag( $gJConfig->jResponseHtml['CSSprepro_compile'] );
            if( $configCompileFlag !== null ) {
                $defaultCompileFlag = $configCompileFlag;
            }
        }

        $subPlugins = array();
        foreach( $gJConfig->jResponseHtml['CSSprepro_plugins'] as $subPluginName ) {
            $subPlugin = jApp::loadPlugin($subPluginName, 'CSSprepro', '.CSSprepro.php', $subPluginName.'CSSpreproPlugin', $this);
            if( $subPlugin ) {
                $subPlugins[$subPluginName] = $subPlugin;
            } else {
                trigger_error( "CSSprepro plugin could not find sub-plugin '$subPluginName'", E_USER_ERROR );
            }
        }

        $inputCSSLinks = $this->response->getCSSLinks();
        $outputCSSLinks = array();

        foreach( $inputCSSLinks as $inputCSSLinkUrl=>$CSSLinkParams ) {
            $CSSLinkUrl = $inputCSSLinkUrl;
            foreach( $subPlugins as $subPluginName=>$subPlugin ) {
                if( $subPlugin->handles( $inputCSSLinkUrl, $CSSLinkParams ) ) {
                    //we suppose url starts with basepath ...
                    if( substr($CSSLinkUrl, 0, strlen($gJConfig->urlengine['basePath'])) != $gJConfig->urlengine['basePath'] ) {
                        throw new Exception("File $CSSLinkUrl seems not to be located in your basePath : it can not be processed with CSSprepro");
                    } else {
                        $compileFlag = $defaultCompileFlag;
                        if( isset($gJConfig->jResponseHtml['CSSprepro_'.$subPluginName.'_compile']) ) {
                            $pluginCompileFlag = $this->translateCompileFlag( $gJConfig->jResponseHtml['CSSprepro_'.$subPluginName.'_compile'] );
                            if( $pluginCompileFlag !== null ) {
                                $compileFlag = $pluginCompileFlag;
                            }
                        }

                        $filePath = jApp::wwwPath() . substr($CSSLinkUrl, strlen($gJConfig->urlengine['basePath']));

                        $outputSuffix = '.css';
                        $outputPath = $filePath . $outputSuffix;

                        try {
                            $compile = true;
                            if( is_file($outputPath) ) {
                                if( ($compileFlag == CSSPREPRO_COMPILE_ALWAYS) ) {
                                    unlink($outputPath);
                                } elseif( ($compileFlag == CSSPREPRO_COMPILE_ONCE) ) {
                                    $compile = false;
                                }
                                //CSSPREPRO_COMPILE_ONCHANGE is CSSprepro's natural behaviour. So we let him do ...
                            }
                            if( $compile ) {
                                $subPlugin->compile($filePath, $outputPath);
                            }
                            $CSSLinkUrl = $CSSLinkUrl . $outputSuffix;
                        } catch (exception $ex) {
                            trigger_error("CSSprepro fatal error on file $filePath:<br />".$ex->getMessage(), E_USER_ERROR);
                        }
                    }
                    $subPlugin->cleanCSSLinkParams( $CSSLinkParams );
                    break;
                }
            }

            $outputCSSLinks[$CSSLinkUrl] = $CSSLinkParams;
        }

        $this->response->setCSSLinks( $outputCSSLinks );
    }


    /**
     * called just before the output of an error page
     */
    public function atBottom() {
    }

    /**
     * called just before the output of an error page
     */
    public function beforeOutputError() {
    }



    private function translateCompileFlag( $compileFlagString ) {
        $compileFlag = null;

        switch($compileFlagString) {
        case 'always':
            $compileFlag = CSSPREPRO_COMPILE_ALWAYS;
            break;
        case 'onchange':
            $compileFlag = CSSPREPRO_COMPILE_ONCHANGE;
            break;
        case 'once':
            $compileFlag = CSSPREPRO_COMPILE_ONCE;
            break;
        }

        return $compileFlag;
    }
}


