<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @ilCtrl_Calls ilContentPagePageGUI: ilPageEditorGUI, ilEditClipboardGUI, ilMDEditorGUI
 * @ilCtrl_Calls ilContentPagePageGUI: ilPublicUserProfileGUI, ilNoteGUI
 * @ilCtrl_Calls ilContentPagePageGUI: ilPropertyFormGUI, ilInternalLinkGUI, ilPageMultiLangGUI
 */
class ilContentPagePageGUI extends ilPageObjectGUI implements ilContentPageObjectConstants
{
    protected bool $isEmbeddedMode = false;
    protected string $language = '-';

    public function __construct(int $a_id = 0, int $a_old_nr = 0, bool $isEmbeddedMode = false, string $language = '')
    {
        parent::__construct(self::OBJ_TYPE, $a_id, $a_old_nr, false, $language);
        $this->setTemplateTargetVar('ADM_CONTENT');
        $this->setTemplateOutput(false);
        $this->isEmbeddedMode = $isEmbeddedMode;
    }

    public function getProfileBackUrl() : string
    {
        if ($this->isEmbeddedMode) {
            return '';
        }

        return parent::getProfileBackUrl();
    }

    public function setDefaultLinkXml() : void
    {
        parent::setDefaultLinkXml();

        if ($this->isEmbeddedMode) {
            $linkXml = $this->getLinkXML();

            try {
                $linkXml = str_replace('<LinkTargets></LinkTargets>', '', $linkXml);
                
                $domDoc = new DOMDocument();
                $domDoc->loadXML('<?xml version="1.0" encoding="UTF-8"?>' . $linkXml);

                $xpath = new DOMXPath($domDoc);
                $links = $xpath->query('//IntLinkInfos/IntLinkInfo');

                if ($links->length > 0) {
                    foreach ($links as $link) {
                        /** @var DOMNode $link */
                        $link->attributes->getNamedItem('LinkTarget')->nodeValue = '_blank';
                    }
                }

                $linkXmlWithBlankTargets = $domDoc->saveXML();

                $this->setLinkXml(str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $linkXmlWithBlankTargets));
            } catch (Throwable $e) {
                $this->log->error(sprintf(
                    'Could not manipulate page editor link XML: %s / Error Message: %s',
                    $linkXml,
                    $e->getMessage()
                ));
            }
        }
    }

    public function finishEditing() : void
    {
        $this->ctrl->redirectByClass(ilObjContentPageGUI::class, 'view');
    }
}
