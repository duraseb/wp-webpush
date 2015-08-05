<?php

/**
 * @package WebPush
 */

class WebpushWidget extends WP_Widget {
    const DEFAULT_TITLE = 'Direct notifications';
    const DEFAULT_TEXT = 'Subscribe to direct desktop or mobile notifications';
    const DEFAULT_BUTTON_TEXT = 'Subscribe';

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
        if (!empty($instance['title'])) {
            echo $args['before_title'];
            echo esc_html($instance['title']);
            echo $args['after_title'];
        }
        if (!empty($instance['text'])) {
            echo esc_html($instance['text']);
        }
        if (!empty($instance['button_text'])) {
            $button_text = $instance['button_text'];
        } else {
            $button_text = __(self::DEFAULT_BUTTON_TEXT);
        }
        ?>
        <div class="webpush_subscribe"><button class="webpush_subscribe_button"><?php echo esc_html($button_text); ?></button></div>
        <?php
        echo $args['after_widget'];
    }

    function form($instance) {
        if ($instance) {
          $title = isset($instance['title']) ? $instance['title'] : '';
          $text = isset($instance['text']) ? $instance['text'] : '';
          $button_text = isset($instance['button_text']) ? $instance['button_text'] : '';
        }
        else {
          $title = __(self::DEFAULT_TITLE);
          $text = __(self::DEFAULT_TEXT);
          $button_text = __(self::DEFAULT_BUTTON_TEXT);
        }
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:'); ?></label>
        <textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_html($text); ?></textarea>
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Button text:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo esc_attr($button_text); ?>" />
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['button_text'] = strip_tags($new_instance['button_text']);
        $instance['text'] = $new_instance['text'];
        return $instance;
    }

}
