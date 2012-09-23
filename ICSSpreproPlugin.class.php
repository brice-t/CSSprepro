<?php
/**
 * @package     
 * @subpackage  
 * @author      Brice Tencé
 * @copyright   2010-2012 Brice Tencé
 * @link        http://www.jelix.org
 * @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */


/**
 * interface for plugins for CSSprepro plugin, which allows
 * to pre-process a CSS file
 */
interface ICSSpreproPlugin {

    public function __construct(CSSpreproHTMLResponsePlugin $CSSpreproInstance);

    /**
     * returns true if the plugin knows how to pre-process the CSS file
     */
    public function handles( $inputCSSLinkUrl, $CSSLinkParams );

    /**
     * pre-processes a CSS file (should read the file, pre-process it and
     * write the result in an output file)
     */
    public function compile( $filePath, $outputPath );

    /**
     * clean CSSLinkParams if needed (i.e. a param may have been passed to trigger the handle()
     * and should now be cleaned so that it is not output in the markup)
     */
    public function cleanCSSLinkParams( &$CSSLinkParams );
}
