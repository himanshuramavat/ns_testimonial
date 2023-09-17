<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'NsTestimonial',
        'Showcase',
        [
            \NITSAN\NsTestimonial\Controller\TestimonialController::class => 'list, show, new, create, '
        ],
        // non-cacheable actions
        [
            \NITSAN\NsTestimonial\Controller\TestimonialController::class => 'create, '
        ]
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    showcase {
                        iconIdentifier = ns_testimonial-plugin-showcase
                        title = LLL:EXT:ns_testimonial/Resources/Private/Language/locallang_db.xlf:tx_ns_testimonial_showcase.name
                        description = LLL:EXT:ns_testimonial/Resources/Private/Language/locallang_db.xlf:tx_ns_testimonial_showcase.description
                        tt_content_defValues {
                            CType = list
                            list_type = nstestimonial_showcase
                        }
                    }
                }
                show = *
            }
       }'
    );
})();
