![OffsetWP Editor Support](/doc/static/cover-light.png#gh-light-mode-only)
![OffsetWP Editor Support](/doc/static/cover-dark.png#gh-dark-mode-only)

<h1 align="center">
    OffsetWP Editor Support
</h1>

<p align="center">
    A library to manage WordPress editor types and features
</p>

## Installation

```bash
composer require offsetwp/editor-support
```

## Usage

### Basic

```php
use OffsetWP\EditorSupport\EditorSupportManager;

$editor = EditorSupportManager::from_post_type( 'page' );
$editor
    ->set_classic_editor()
    ->remove_comments();
```

### API

#### Selector

```php
$editor = EditorSupportManager::from_post_type( 'page' ); // With post type
$editor = EditorSupportManager::from_post_id( 42 ); // With post ID
$editor = EditorSupportManager::from_template( 'template/contact.php' ); // With template name
```

### Primary editor type

```php
$editor->set_gutenberg_editor(); // Gutenberg
$editor->set_classic_editor(); // Classic Editor
$editor->set_empty_editor(); // No editor
```

> ⚠ If you wish to disable Gutenberg entirely, we recommend using the [Classic Editor](https://fr.wordpress.org/plugins/classic-editor/) plugin.

### Add editor features

```php
$editor
    ->add_title()
    ->add_editor()
    ->add_author()
    ->add_thumbnail()
    ->add_excerpt()
    ->add_trackbacks()
    ->add_custom_fields()
    ->add_comments()
    ->add_revisions()
    ->add_page_attributes()
    ->add_post_formats();
```

### Add custom feature

```php
$editor->add_support( 'my-custom-feature' );
```

### Add all features

```php
$editor
    ->add_all() // Add all features
    ->add_all( array( 'content', 'thumbnail' ) ); // Add all features without specific ones
```

### Remove editor features

```php
$editor
    ->remove_title()
    ->remove_editor()
    ->remove_author()
    ->remove_thumbnail()
    ->remove_excerpt()
    ->remove_trackbacks()
    ->remove_custom_fields()
    ->remove_comments()
    ->remove_revisions()
    ->remove_page_attributes()
    ->remove_post_formats();
```

### Remove custom feature

```php
$editor->remove_support( 'my-custom-feature' );
```

### Remove all features

```php
$editor
    ->remove_all() // Remove all features
    ->remove_all( array( 'content', 'thumbnail' ) ); // Remove all features without specific ones
```
