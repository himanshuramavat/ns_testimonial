<?php
namespace NITSAN\NsTestimonial\Utility;

session_start();

class label
{

    /**
     * @return void
     */
    public function getObjectLabel(&$params, &$pObj)
    {
        if (empty($params)) {
        } else {
            if ($params['table'] != 'tx_nstestimonial_domain_model_nsmedia') {
                return '';
            }

            $rec = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($params['table'], $params['row']['uid']);
            $media = $rec['media'] ?? null;
            if ($media > 0) {
                $totalMedia = $media;
            } else {
                $totalMedia = 0;
            }

            $params['title'] = 'Album Media (' . $media . ')';
        }
    }
}
