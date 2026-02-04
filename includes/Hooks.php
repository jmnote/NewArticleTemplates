<?php

namespace NewArticleTemplates;

use MediaWiki\Content\TextContent;
use MediaWiki\EditPage\EditPage;
use MediaWiki\MediaWikiServices;
use MediaWiki\Output\OutputPage;
use MediaWiki\Title\Title;
use StringUtils;

class Hooks
{
    /**
     * preload returns the text that is in the article specified by $preload
     */
    public static function preload($preload)
    {
        if ($preload === '') {
            return '';
        }

        $preloadTitle = Title::newFromText($preload);
        if (! $preloadTitle) {
            return '';
        }

        $article = MediaWikiServices::getInstance()
            ->getWikiPageFactory()
            ->newFromTitle($preloadTitle);
        if (! $article) {
            return '';
        }

        $content = $article->getContent();
        if (! $content instanceof TextContent) {
            return '';
        }

        $text = $content->getText();
        // Remove <noinclude> sections and <includeonly> tags from text
        $text = StringUtils::delimiterReplace('<noinclude>', '</noinclude>', '', $text);
        $text = strtr($text, ['<includeonly>' => '', '</includeonly>' => '']);

        return $text;
    }

    // https://www.mediawiki.org/wiki/Manual:Hooks/EditPage::showEditForm:initial
    public static function onEditPageShowEditFormInitial(EditPage $editPage, OutputPage $output): void
    {
        global $wgNewArticleTemplatesDefault, $wgNewArticleTemplatesEnabledNamespaces,
        $wgNewArticleTemplatesNamespaceTemplates, $wgNewArticleTemplatesApplyToSubpages;

        $title = $editPage->getTitle();
        if ($title->exists() || $editPage->textbox1 !== '') {
            return;
        }

        /* see if this is a subpage */
        $isSubpage = false;

        if ($title->isSubpage()) {
            $baseTitle = Title::newFromText(
                $title->getBaseText(),
                $title->getNamespace());
            if ($baseTitle && $baseTitle->exists()) {
                $isSubpage = true;
            }
        }

        /* we might want to return if this is a subpage */
        if ((! $wgNewArticleTemplatesApplyToSubpages) && $isSubpage) {
            return;
        }

        $applyAllNamespaces = ! $wgNewArticleTemplatesEnabledNamespaces;

        $ns = $title->getNamespace();
        if (! $applyAllNamespaces && ! in_array($ns, $wgNewArticleTemplatesEnabledNamespaces, true)) {
            return;
        }

        $template = $wgNewArticleTemplatesNamespaceTemplates[$ns] ?? $wgNewArticleTemplatesDefault;
        if (! $template) {
            return;
        }

        /* if this is a subpage, we want to to use $template/Subpage instead, if it exists */
        if ($isSubpage) {
            $subpageTitle = Title::newFromText($template.'/Subpage');
            if ($subpageTitle && $subpageTitle->exists()) {
                $template = $template.'/Subpage';
            }
        }
        $editPage->textbox1 = self::preload($template);
    }
}
