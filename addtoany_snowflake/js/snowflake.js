(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.addtoanySnowflake = {
    /**
     * This adds the custom styles to the head. Note that addtoany does this in
     * hook_page_attachments. Therefore, It's possible that gloab addtoany custom css will
     * override this. It is advised that you only add custom styles to a snowflake block,
     * not to addtoany global settings.
     *
     * Custom js is handled only by addtoany, not the snowflake module.
     */
    attach: function(context, drupalSettings) {
      $("<style type='text/css'>" + drupalSettings.addtoanySnowflake.css + "</style>").appendTo("head");
    },
  }
})(jQuery, Drupal, drupalSettings);
