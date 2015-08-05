<?php

/**
 * @package WebPush
 */

class WebpushWidget extends WP_Widget {
    const DEFAULT_TITLE = 'Direct notifications';
    const DEFAULT_TEXT = 'Subscribe to new content notifications on your desktop or mobile browser';
    const DEFAULT_BUTTON_TEXT = 'Subscribe';
    const DEFAULT_BUTTON_TEXT_UNSUBSCRIBE = 'Unsubscribe';

    public function __construct() {
        parent::__construct(
            'webpush_widget',
            __('WebPush Subscribe'),
            array('description' => __('Shows a Web Push subscribe button'))
        );

        if (is_active_widget(false, false, $this->id_base)) {
          add_action('wp_head', array($this, 'css'));
          add_action('wp_head', array($this, 'js'));
        }
    }

    public function css() {
        ?>
        <style>
        .widget.widget_webpush_widget {display: none;}
        </style>
        <?php
    }

    public function js() {
        ?>
        <script>
            var webpush_endpoint = '<?php echo addslashes(site_url()); ?>';
        </script>
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
        if (!empty($instance['button_text_unsubscribe'])) {
            $button_text_unsubscribe = $instance['button_text_unsubscribe'];
        } else {
            $button_text_unsubscribe = __(self::DEFAULT_BUTTON_TEXT_UNSUBSCRIBE);
        }
        ?>
        <div class="webpush_subscribe"><button class="webpush_subscribe_button" data-subscribe="<?php echo esc_attr($button_text); ?>" data-unsubscribe="<?php echo esc_attr($button_text_unsubscribe); ?>"></button></div>
        <?php
        echo $args['after_widget'];
    }

    function form($instance) {
        if ($instance) {
          $title = isset($instance['title']) ? $instance['title'] : '';
          $text = isset($instance['text']) ? $instance['text'] : '';
          $button_text = isset($instance['button_text']) ? $instance['button_text'] : '';
          $button_text_unsubscribe = isset($instance['button_text_unsubscribe']) ? $instance['button_text_unsubscribe'] : '';
        }
        else {
          $title = __(self::DEFAULT_TITLE);
          $text = __(self::DEFAULT_TEXT);
          $button_text = __(self::DEFAULT_BUTTON_TEXT);
          $button_text_unsubscribe = __(self::DEFAULT_BUTTON_TEXT_UNSUBSCRIBE);
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
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Subscribe button:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo esc_attr($button_text); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Unsubscribe button:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('button_text_unsubscribe'); ?>" name="<?php echo $this->get_field_name('button_text_unsubscribe'); ?>" type="text" value="<?php echo esc_attr($button_text_unsubscribe); ?>" />
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['button_text'] = strip_tags($new_instance['button_text']);
        $instance['button_text_unsubscribe'] = strip_tags($new_instance['button_text_unsubscribe']);
        $instance['text'] = $new_instance['text'];
        return $instance;
    }

}
