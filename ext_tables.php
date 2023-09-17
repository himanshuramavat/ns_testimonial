<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'NsTestimonial',
        'nitsan',
        'testimonial',
        '',
        [
            \NITSAN\NsTestimonial\Controller\TestimonialBackendController::class => 'dashboard, saveConstant, list, premiumExtension, commonSettings',
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:ns_testimonial/Resources/Public/Icons/user_mod_testimonial.svg',
            'labels' => 'LLL:EXT:ns_testimonial/Resources/Private/Language/locallang_testimonial.xlf',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement' ,
            'inheritNavigationComponentFromMainModule' => false
        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_nstestimonial_domain_model_testimonial', 'EXT:ns_testimonial/Resources/Private/Language/locallang_csh_tx_nstestimonial_domain_model_testimonial.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_nstestimonial_domain_model_testimonial');
})();
