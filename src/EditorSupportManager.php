<?php
/**
 * OffsetWP Editor Support
 *
 * @package OffsetWP\EditorSupport
 */

declare(strict_types=1);

namespace OffsetWP\EditorSupport;

/**
 * Class EditorSupportManager
 *
 * @package OffsetWP\EditorSupport
 */
class EditorSupportManager {
	/**
	 * Instance type
	 *
	 * @var string
	 */
	private string $instance_type = '';

	/**
	 * Instance value
	 *
	 * @var string|int
	 */
	private string|int $instance_value = '';

	/**
	 * The editor type
	 *
	 * @var 'gutenberg'|'classic'|'empty'
	 */
	private string $editor_type = '';

	/**
	 * The editor support
	 *
	 * @var array<string, string>
	 */
	private array $supports = array();

	/**
	 * EditorSupport instances
	 *
	 * @var mixed
	 */
	private static array $instances = array();

	/**
	 * Register the hooks
	 */
	private function __construct() {
		add_action( 'admin_init', \Closure::fromCallable( array( $this, 'hook_add_or_remove_support' ) ), 10 );
		add_filter( 'register_post_type_args', \Closure::fromCallable( array( $this, 'hook_enable_gutenberg' ) ), 10, 2 );
		add_filter( 'gutenberg_can_edit_post_type', \Closure::fromCallable( array( $this, 'hook_disable_gutenberg' ) ), 10, 2 );
		add_filter( 'use_block_editor_for_post_type', \Closure::fromCallable( array( $this, 'hook_disable_gutenberg' ) ), 10, 2 );
	}

	/**
	 * The EditorSupport instance
	 *
	 * @param string $post_type Post type.
	 * @return self
	 */
	public static function from_post_type( string $post_type ): self {
		$instance                 = self::from( 'post_type', $post_type );
		$instance->instance_type  = 'post_type';
		$instance->instance_value = $post_type;
		return $instance;
	}

	/**
	 * The EditorSupport instance
	 *
	 * @param int $post_id Post id.
	 * @return self
	 */
	public static function from_post_id( int $post_id ): self {
		$instance                 = self::from( 'post_id', $post_id );
		$instance->instance_type  = 'post_id';
		$instance->instance_value = $post_id;
		return $instance;
	}

	/**
	 * The EditorSupport instance
	 *
	 * @param string $template Template name.
	 * @return self
	 */
	public static function from_template( string $template ): self {
		$instance                 = self::from( 'template', $template );
		$instance->instance_type  = 'template';
		$instance->instance_value = $template;
		return $instance;
	}

	/**
	 * The EditorSupport instance
	 *
	 * @param 'post_type'|'post_id'|'template' $type The instance type.
	 * @param string|int                       $value The instance value.
	 * @return self
	 */
	private static function from( string $type, string|int $value ): self {
		$instance = self::find_instance( $type, $value );

		if ( empty( $instance ) ) {
			self::$instances[] = array(
				'type'     => $type,
				'value'    => $value,
				'instance' => new self(),
			);

			$instance = end( self::$instances );
		}

		return $instance['instance'];
	}

	/**
	 * Find the instance
	 *
	 * @param string     $instance_type The instance type.
	 * @param string|int $instance_value The instance value.
	 * @return self|null
	 */
	private static function find_instance( string $instance_type, string|int $instance_value ): self|null {
		foreach ( self::$instances as $instance ) {
			if ( $instance_type === $instance['type'] && $instance_value === $instance['value'] ) {
				return $instance;
			}
		}
		return null;
	}

	/**
	 * Set the Gutenberg editor
	 *
	 * @return self
	 */
	public function set_gutenberg_editor(): self {
		$this->editor_type = 'gutenberg';
		$this->add_support( 'editor' );
		return $this;
	}

	/**
	 * Set the classic editor
	 *
	 * @return self
	 */
	public function set_classic_editor(): self {
		$this->editor_type = 'classic';
		return $this;
	}

	/**
	 * Set the empty editor (without content)
	 *
	 * @return self
	 */
	public function set_empty_editor(): self {
		$this->editor_type = 'empty';
		$this->remove_support( 'editor' );
		return $this;
	}

	/**
	 * Remove WordPress "title" support
	 *
	 * @return self
	 */
	public function add_title(): self {
		return $this->add_support( 'title' );
	}

	/**
	 * Remove WordPress "editor" support
	 *
	 * @return self
	 */
	public function add_editor(): self {
		return $this->add_support( 'editor' );
	}

	/**
	 * Remove WordPress "author" support
	 *
	 * @return self
	 */
	public function add_author(): self {
		return $this->add_support( 'author' );
	}

	/**
	 * Remove WordPress "thumbnail" support
	 *
	 * @return self
	 */
	public function add_thumbnail(): self {
		return $this->add_support( 'thumbnail' );
	}

	/**
	 * Remove WordPress "excerpt" support
	 *
	 * @return self
	 */
	public function add_excerpt(): self {
		return $this->add_support( 'excerpt' );
	}

	/**
	 * Remove WordPress "trackbacks" support
	 *
	 * @return self
	 */
	public function add_trackbacks(): self {
		return $this->add_support( 'trackbacks' );
	}

	/**
	 * Remove WordPress "custom-fields" support
	 *
	 * @return self
	 */
	public function add_custom_fields(): self {
		return $this->add_support( 'custom-fields' );
	}

	/**
	 * Remove WordPress "comments" support
	 *
	 * @return self
	 */
	public function add_comments(): self {
		return $this->add_support( 'comments' );
	}

	/**
	 * Remove WordPress "revisions" support
	 *
	 * @return self
	 */
	public function add_revisions(): self {
		return $this->add_support( 'revisions' );
	}

	/**
	 * Remove WordPress "page-attributes" support
	 *
	 * @return self
	 */
	public function add_page_attributes(): self {
		return $this->add_support( 'page-attributes' );
	}

	/**
	 * Remove WordPress "post-formats" support
	 *
	 * @return self
	 */
	public function add_post_formats(): self {
		return $this->add_support( 'post-formats' );
	}

	/**
	 * Add WordPress support
	 *
	 * @param string $feature The WordPress feature.
	 * @return self
	 */
	public function add_support( string $feature ): self {
		$this->supports[ $feature ] = 'add';
		return $this;
	}

	/**
	 * Add all features
	 *
	 * @param array<string> ...$excluded_features Excluded features.
	 * @return self
	 */
	public function add_all( ...$excluded_features ): self {
		$supports = array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'trackbacks',
			'custom-fields',
			'comments',
			'revisions',
			'page-attributes',
			'post-formats',
		);

		foreach ( $supports as $support ) {
			if ( ! in_array( $support, $excluded_features, true ) ) {
				$this->add_support( $support );
			}
		}

		return $this;
	}

	/**
	 * Remove WordPress "title" support
	 *
	 * @return self
	 */
	public function remove_title(): self {
		return $this->remove_support( 'title' );
	}

	/**
	 * Remove WordPress "editor" support
	 *
	 * @return self
	 */
	public function remove_editor(): self {
		return $this->remove_support( 'editor' );
	}

	/**
	 * Remove WordPress "author" support
	 *
	 * @return self
	 */
	public function remove_author(): self {
		return $this->remove_support( 'author' );
	}

	/**
	 * Remove WordPress "thumbnail" support
	 *
	 * @return self
	 */
	public function remove_thumbnail(): self {
		return $this->remove_support( 'thumbnail' );
	}

	/**
	 * Remove WordPress "excerpt" support
	 *
	 * @return self
	 */
	public function remove_excerpt(): self {
		return $this->remove_support( 'excerpt' );
	}

	/**
	 * Remove WordPress "trackbacks" support
	 *
	 * @return self
	 */
	public function remove_trackbacks(): self {
		return $this->remove_support( 'trackbacks' );
	}

	/**
	 * Remove WordPress "custom-fields" support
	 *
	 * @return self
	 */
	public function remove_custom_fields(): self {
		return $this->remove_support( 'custom-fields' );
	}

	/**
	 * Remove WordPress "comments" support
	 *
	 * @return self
	 */
	public function remove_comments(): self {
		return $this->remove_support( 'comments' );
	}

	/**
	 * Remove WordPress "revisions" support
	 *
	 * @return self
	 */
	public function remove_revisions(): self {
		return $this->remove_support( 'revisions' );
	}

	/**
	 * Remove WordPress "page-attributes" support
	 *
	 * @return self
	 */
	public function remove_page_attributes(): self {
		return $this->remove_support( 'page-attributes' );
	}

	/**
	 * Remove WordPress "post-formats" support
	 *
	 * @return self
	 */
	public function remove_post_formats(): self {
		return $this->remove_support( 'post-formats' );
	}

	/**
	 * Remove all features
	 *
	 * @param array<string> ...$excluded_features Excluded features.
	 * @return self
	 */
	public function remove_all( ...$excluded_features ): self {
		$supports = array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'trackbacks',
			'custom-fields',
			'comments',
			'revisions',
			'page-attributes',
			'post-formats',
		);

		foreach ( $supports as $support ) {
			if ( ! in_array( $support, $excluded_features, true ) ) {
				$this->remove_support( $support );
			}
		}

		return $this;
	}

	/**
	 * Remove WordPress support
	 *
	 * @param string $feature The WordPress feature.
	 * @return self
	 */
	public function remove_support( string $feature ): self {
		$this->supports[ $feature ] = 'remove';
		return $this;
	}

	/**
	 * Check is good instance
	 *
	 * @param string $post_type The post type.
	 * @param int    $post_id The post id.
	 * @param string $template The template name.
	 * @return bool If the instance is the good one.
	 */
	private function check_is_good_instance( string $post_type = '', int $post_id = 0, string $template = '' ): bool {
		if ( 'post_type' === $this->instance_type && $post_type === $this->instance_value ) {
			return true;
		}

		if ( 'post_id' === $this->instance_type && $post_id === $this->instance_value ) {
			return true;
		}

		if ( 'template' === $this->instance_type && $template === $this->instance_value ) {
			return true;
		}

		return false;
	}

	/**
	 * Add or remove support
	 */
	private function hook_add_or_remove_support(): void {
		global $pagenow;
		$post_type = '';
		$post_id   = 0;
		$template  = '';

		if ( 'post.php' === $pagenow && ! empty( $_GET ['post'] ) ) {
			$post_id   = ! empty( $_GET ['post'] ) ? (int) $_GET ['post'] : 0;
			$post_type = (string) get_post_type( $post_id );
			$template  = get_page_template_slug( $post_id );
			$template  = is_string( $template ) ? $template : '';
		}

		if ( 'post-new.php' === $pagenow ) {
			$post_type = ! empty( $_GET ['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET ['post_type'] ) ) : 'post';
		}

		if ( ! $this->check_is_good_instance( $post_type, $post_id, $template ) ) {
			return;
		}

		foreach ( $this->supports as $feature => $action ) {
			if ( 'add' === $action ) {
				add_post_type_support( $post_type, $feature );
			} else {
				remove_post_type_support( $post_type, $feature );
			}
		}
	}

	/**
	 * Enable Gutenberg
	 *
	 * @param array  $args Post type arguments.
	 * @param string $post_type Post type name.
	 * @return array Post type arguments.
	 */
	private function hook_enable_gutenberg( array $args, string $post_type ) {
		if ( ! is_admin() ) {
			return $args;
		}

		global $pagenow;
		$post_type = '';
		$post_id   = 0;
		$template  = '';

		if ( 'post.php' === $pagenow && ! empty( $_GET ['post'] ) ) {
			$post_id   = ! empty( $_GET ['post'] ) ? (int) $_GET ['post'] : 0;
			$post_type = (string) get_post_type( $post_id );
			$template  = get_page_template_slug( $post_id );
			$template  = is_string( $template ) ? $template : '';
		}

		if ( 'post-new.php' === $pagenow && ! empty( $_GET ['post_type'] ) ) {
			$post_type = ! empty( $_GET ['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET ['post_type'] ) ) : 'post';
		}

		if ( ! $this->check_is_good_instance( $post_type, $post_id, $template ) ) {
			return $args;
		}

		if ( 'gutenberg' === $this->editor_type ) {
			$args['show_in_rest'] = true;
		}

		return $args;
	}

	/**
	 * Disable Gutenberg
	 *
	 * @param bool   $can_edit  Can edit.
	 * @param string $post_type Post type.
	 * @return bool.
	 */
	private function hook_disable_gutenberg( bool $can_edit, string $post_type ): bool {
		$post_id  = ! empty( $_GET ['post'] ) ? (int) $_GET ['post'] : 0;
		$template = get_page_template_slug( $post_id );
		$template = is_string( $template ) ? $template : '';

		if ( ! $this->check_is_good_instance( $post_type, $post_id, $template ) ) {
			return $can_edit;
		}

		if ( 'classic' === $this->editor_type || 'empty' === $this->editor_type ) {
			return false;
		}

		return $can_edit;
	}
}
