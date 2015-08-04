<?php

/**
 * @package WebPush
 */

class WebpushWidget extends WP_Widget {
  public function __construct() {
    parent::__construct(
        'webpush_widget',
        __('WebPush Subscribe'),
        array('description' => __('Shows a Web Push subscribe button'))
    );

    if (is_active_widget(false, false, $this->id_base)) {
      add_action('wp_head', array($this, 'css'));
    }
  }

  public function css() {
?>
<?php
  }

  function widget($args, $instance) {
    echo $args['before_widget'];
    if (!empty( $instance['title'])) {
      echo $args['before_title'];
      echo esc_html($instance['title']);
      echo $args['after_title'];
    }
    ?>
    <div class="webpush_subscribe"><button class="webpush_subscribe_button">Subscribe</button></div>
    <?php
    echo $args['after_widget'];
  }

  function form($instance) {
    if ($instance) {
      $title = esc_attr($instance['title']);
    }
    else {
      $title = __('Subscribe to notifications');
    }
?>
    <p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php
    }

  function update($new_instance, $old_instance) {
    $instance['title'] = strip_tags($new_instance['title']);
    return $instance;
  }

}
