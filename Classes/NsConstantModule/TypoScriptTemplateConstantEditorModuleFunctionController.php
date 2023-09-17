<?php
namespace NITSAN\NsTestimonial\NsConstantModule;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * TypoScript Constant editor
 * @internal This is a specific Backend Controller implementation and is not considered part of the Public TYPO3 API.
 */
class TypoScriptTemplateConstantEditorModuleFunctionController
{
    /**
      * @var TypoScriptTemplateModuleController
      */
    protected $pObj;

    /**
     * The currently selected sys_template record
     * @var array
     */
    protected $templateRow;

    /**
     * @var ExtendedTemplateService
     */
    protected $templateService;

    /**
     * @var array
     */
    protected $constants;

    /**
     * @var int GET/POST var 'id'
     */
    protected $id;

    /**
     * Init, called from parent object
     *
     * @param TypoScriptTemplateModuleController $pObj A reference to the parent (calling) object
     */
    public function init($pObj)
    {
        $this->pObj = $pObj;
        $this->id = (int)GeneralUtility::_GP('id');
    }

    /**
     * Initialize editor
     *
     * Initializes the module.
     * Done in this function because we may need to re-initialize if data is submitted!
     *
     * @param int $pageId
     * @param int $template_uid
     * @return bool
     */
    protected function initialize_editor($pageId, $template_uid = 0)
    {
        $this->templateService = GeneralUtility::makeInstance(ExtendedTemplateService::class);

        // Get the row of the first VISIBLE template of the page. whereclause like the frontend.
        $this->templateRow = $this->templateService->ext_getFirstTemplate($pageId, $template_uid);
        // IF there was a template...
        if (is_array($this->templateRow)) {
            // Gets the rootLine
            $rootlineUtility = GeneralUtility::makeInstance(RootlineUtility::class, $pageId);
            $rootLine = $rootlineUtility->get();
            // This generates the constants/config + hierarchy info for the template.
            $this->templateService->runThroughTemplates($rootLine, $template_uid);
            // The editable constants are returned in an array.
            $this->constants = $this->templateService->generateConfig_constants();
            // The returned constants are sorted in categories, that goes into the $tmpl->categories array
            $this->templateService->ext_categorizeEditableConstants($this->constants);
            // This array will contain key=[expanded constant name], value=line number in template.
            $this->templateService->ext_regObjectPositions($this->templateRow['constants']);
            return true;
        }
        return false;
    }
    /**
     * Main, called from parent object
     *
     * @return string
     */
    public function main()
    {
        $assigns = [];
        $assigns['LLPrefix'] = 'LLL:EXT:tstemplate/Resources/Private/Language/locallang_ceditor.xlf:';

        // initialize
        $template_uid = isset($template_uid) ? $template_uid : "";
        $existTemplate = $this->initialize_editor($this->id, $template_uid);
        if ($existTemplate) {
            $saveId = $this->templateRow['_ORIG_uid'] ?: $this->templateRow['uid'];
            // Update template ?
            if (GeneralUtility::_POST('_savedok')) {
                $this->templateService->changed = 0;
                $this->templateService->ext_procesInput(GeneralUtility::_POST(), [], $this->constants, $this->templateRow);
                if ($this->templateService->changed) {
                    // Set the data to be saved
                    $recData = [];
                    if (PHP_VERSION >= 7.4) {
                        $recData['sys_template'][$saveId]['constants'] = implode(LF, $this->templateService->raw);
                    } else {
                        $recData['sys_template'][$saveId]['constants'] = implode($this->templateService->raw, LF);
                    }
                    // Create new  tce-object
                    $tce = GeneralUtility::makeInstance(DataHandler::class);
                    $tce->start($recData, []);
                    $tce->process_datamap();
                    // Clear the cache (note: currently only admin-users can clear the cache in tce_main.php)
                    $tce->clear_cacheCmd('all');
                    // re-read the template ...
                    // re-read the constants as they have changed
                    $this->initialize_editor($this->id, $template_uid);
                }
            }
            if (version_compare(TYPO3_branch, '11', '>=')) {
                if (empty($this->pObj)) {
                    $iconFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconFactory::class);
                    $pageRenderer = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
                    $uriBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);
                    $moduleTemplateFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Template\ModuleTemplateFactory::class);
                    $this->pObj = new \TYPO3\CMS\Tstemplate\Controller\TypoScriptTemplateModuleController($iconFactory,$pageRenderer,$uriBuilder,$moduleTemplateFactory);
                }
            } else {
                if (empty($this->pObj)) {
                    $this->pObj = new \TYPO3\CMS\Tstemplate\Controller\TypoScriptTemplateModuleController;
                }
            }
            $this->pObj->MOD_SETTINGS = BackendUtility::getModuleData($this->pObj->MOD_MENU, GeneralUtility::_GP('SET'), 'web_ts');
            // Resetting the menu (stop)
            if (!empty($this->pObj->MOD_MENU['constant_editor_cat'])) {
                $assigns['constantsMenu'] = BackendUtility::getDropdownMenu($this->id, 'SET[constant_editor_cat]', $this->pObj->MOD_SETTINGS['constant_editor_cat'], $this->pObj->MOD_MENU['constant_editor_cat']);
            }

            $category = strtolower(GeneralUtility::_GP('cat'));

            // Category and constant editor config:
            $printFields = trim($this->templateService->ext_printFields($this->constants, $category));
            foreach ($this->templateService->getInlineJavaScript() as $name => $inlineJavaScript) {
                $this->getPageRenderer()->addJsInlineCode($name, $inlineJavaScript);
            }
            $theOutput = $printFields;
        } else {
            $view = GeneralUtility::makeInstance(StandaloneView::class);
            $id = (int)GeneralUtility::_GP('id');
            if ($id) {
                $urlParameters = [
                    'id' => $id,
                    'template' => 'all'
                ];
                if (version_compare(TYPO3_branch, '10', '>=')) {
                    $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
                    $aHref = (string)$uriBuilder->buildUriFromRoute('web_ts', $urlParameters);
                } else {
                    $aHref = (string)BackendUtility::getModuleUrl('web_ts', $urlParameters);
                }
                $view->assign('link', $aHref);
            }
            $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
                'EXT:ns_testimonial/Resources/Private/Backend/Templates/NoConstant.html'
            ));
            $theOutput = $view->render();
        }
        return $theOutput;
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return PageRenderer
     */
    protected function getPageRenderer(): PageRenderer
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }
}
