# NewArticleTemplates extension for MediaWiki

Automatically prefill the edit form for newly created pages using the contents of a template page. You can scope templates by namespace and optionally use a different template for subpages.

Project page:
https://www.mediawiki.org/wiki/Extension:NewArticleTemplates

## Installation

Enable the extension in `LocalSettings.php`:

```php
wfLoadExtension( 'NewArticleTemplates' );
```

## Configuration

All configuration lives in `LocalSettings.php`.

Minimal setup (applies everywhere):

```php
$wgNewArticleTemplatesDefault = 'Template:New page';
```

Full setup:

```php
$wgNewArticleTemplatesDefault = 'Template:New page';
$wgNewArticleTemplatesEnabledNamespaces = [ NS_MAIN, NS_HELP, NS_PROJECT ]; // Empty/unset applies to all namespaces.
$wgNewArticleTemplatesNamespaceTemplates = [
    NS_MAIN => 'Template:New article',
    NS_HELP => 'Template:Help page',
];
$wgNewArticleTemplatesApplyToSubpages = true; // Default: true
```

### Notes

- Templates are plain wiki pages. The extension loads the template content and strips `<noinclude>` and `<includeonly>` tags.
- For subpages, if `<template>/Subpage` exists, it will be used instead of the base template.
- Subpages are included by default. Set `$wgNewArticleTemplatesApplyToSubpages = false;` to opt out.
- If a namespace has a specific entry in `$wgNewArticleTemplatesNamespaceTemplates`, that template takes precedence over `$wgNewArticleTemplatesDefault`.
- In the full setup above, `NS_PROJECT` is enabled but has no specific mapping, so it uses `$wgNewArticleTemplatesDefault`.
- If `$wgNewArticleTemplatesEnabledNamespaces` is empty or unset, templates apply to all namespaces.
- Template titles must match the actual page title (including case and spacing).
