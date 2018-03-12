<?php
namespace Grav\Plugin;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Data\Blueprints;
use RocketTheme\Toolbox\Event\Event;

/**
 * [Tiny SEO Plugin]
 *
 * [Provide a simple way to manage SEO from admin]
 *
 * Class TinyseoPlugin
 * @package Grav\Plugin
 * @license MIT License by jimblue
 */
class TinyseoPlugin extends Plugin
{

  /**
   * @return array
   *
   * The getSubscribedEvents() gives the core a list of events
   *     that the plugin wants to listen to. The key of each
   *     array section is the event that the plugin listens to
   *     and the value (in the form of an array) contains the
   *     callable (or function) as well as the priority. The
   *     higher the number the higher the priority.
   */
  public static function getSubscribedEvents()
  {
    return [
      'onPluginsInitialized' => ['onPluginsInitialized', 0]
    ];
  }

  /**
   * Initialize the plugin
   */
  public function onPluginsInitialized()
  {
    // If we are in admin
    if ($this->isAdmin()) {
      // Enable the main event we are interested in
      $this->enable([
        'onBlueprintCreated' => ['onBlueprintCreated', 0]
      ]);

      return;
    }

    // If plugin is enabled and if we are not in admin
    if ($this->config['plugins.tinyseo.enabled']) {
      // Enable the main event we are interested in
      $this->enable([
        'onPageInitialized' => ['onPageInitialized', 0]
      ]);
    }
  }

  /**
   * On Page Initialized Hook
   */
  public function onPageInitialized()
  {
    $page = $this->grav['page'];
    $meta = $page->metadata();
    $header = $page->header();
    $config = $this->mergeConfig($page);

    // Define page title and page description once
    $pageTitle = $this->getPageTitle();
    $pageDescription = $this->getPageDescription();

    /**
     * Set metas
     */
    $meta = $this->getRobotsMeta($meta, $header, $config);
    $meta = $this->getDescriptionMeta($meta, $pageDescription);
    $meta = $this->getOpenGraphMeta($meta, $pageTitle, $pageDescription, $config);
    $meta = $this->getFacebookMeta($meta, $config);
    $meta = $this->getTwitterMeta($meta, $config);

    // Return updated meta
    $page->metadata($meta);
  }

  /**
   * On Blueprint Created Hook
   */
  public function onBlueprintCreated(Event $event)
  {
    static $inEvent = false;

    // Add Tinyseo tab if page is not a modular
    if (0 !== strpos($event['type'], 'modular/')) {
      $blueprint = $event['blueprint'];

      if (!$inEvent && $blueprint->get('form/fields/tabs', null, '/')) {
        $inEvent = true;
        $blueprints = new Blueprints(__DIR__ . '/blueprints/');
        $extends = $blueprints->get($this->name);
        $blueprint->extend($extends, true);
        $inEvent = false;
      }
    }
  }

  /**
   * Get Robots metadata
   */
  private function getRobotsMeta($meta, $header, $config)
  {
    function metaRobotsEnabled($param)
    {
      return !empty(array_filter($param, function ($value) {
        return $value === true;
      }));
    }

    $pageMetaRobotsEnabled = isset($header->meta_robots) ? metaRobotsEnabled($header->meta_robots) : false;
    $pluginMetaRobotsEnabled = metaRobotsEnabled($config['meta_robots']);

    if ($pageMetaRobotsEnabled) $metaRobots = $header->meta_robots;
    elseif ($pluginMetaRobotsEnabled) $metaRobots = $config['meta_robots'];


    if (isset($metaRobots)) {
      $filteredArray = array_keys($metaRobots, true);
      $robotsDescription = implode(', ', $filteredArray);

      $meta['robots'] = [
        'name' => 'robots',
        'content' => $robotsDescription
      ];
    }

    return $meta;
  }

  /**
   * Get description metadata
   */
  private function getDescriptionMeta($meta, $pageDescription)
  {
    $meta['description'] = [
      'name' => 'description',
      'content' => $pageDescription
    ];

    return $meta;
  }

  /**
   * Get OpenGraph metadata
   */
  private function getOpenGraphMeta($meta, $pageTitle, $pageDescription, $config)
  {
    // OpenGraph type meta
    $meta['og:type'] = [
      'property' => 'og:type',
      'content' => 'article'
    ];

    // OpenGraph url meta
    $meta['og:url'] = [
      'property' => 'og:url',
      'content' => $this->grav['uri']->url(true)
    ];

    // OpenGraph title meta
    $meta['og:title'] = [
      'property' => 'og:title',
      'content' => $pageTitle
    ];

    // OpenGraph description meta
    $meta['og:description'] = [
      'property' => 'og:description',
      'content' => $pageDescription
    ];

    // OpenGraph image meta
    if ($this->getImage()) {
      $meta['og:image'] = [
        'property' => 'og:image',
        'content' => $this->getImage()
      ];
    }

    return $meta;
  }

  /**
   * Get Facebook metadata
   */
  private function getFacebookMeta($meta, $config)
  {
    // Facebook id
    if ($config['facebookid']) {
      $meta['fb:app_id'] = [
        'property' => 'fb:app_id',
        'content' => $config['facebookid']
      ];
    }

    return $meta;
  }

  /**
   * Get Twitter metadata
   */
  private function getTwitterMeta($meta, $config)
  {
    // Twitter id
    if ($config['twitterid']) {
      $meta['twitter:site'] = [
        'property' => 'twitter:site',
        'content' => $config['twitterid']
      ];
    }

    // Twitter card type
    $meta['twitter:card'] = [
      'property' => 'twitter:card',
      'content' => $config['twitter_card_type'] ? : 'summary_large_image'
    ];

    return $meta;
  }

  /**
   * Clean markdown
   */
  public static function cleanMarkdown($text)
  {
    $rules = array(
      '/(#+)(.*)/' => '\2', // headers
      '/(&lt;|<)!--\n((.*|\n)*)\n--(&gt;|\>)/' => '', // comments
      '/(\*|-|_){3}/' => '', // hr
      '/!\[([^\[]+)\]\(([^\)]+)\)/' => '', // images
      '/\[([^\[]+)\]\(([^\)]+)\)/' => '\1', // links
      '/(\*\*|__)(.*?)\1/' => '\2', // bold
      '/(\*|_)(.*?)\1/' => '\2', // emphasis
      '/\~\~(.*?)\~\~/' => '\1', // del
      '/\:\"(.*?)\"\:/' => '\1', // quote
      '/```(.*)\n((.*|\n)+)\n```/' => '\2', // fence code
      '/`(.*?)`/' => '\1', // inline code
      '/(\*|\+|-)(.*)/' => '\2', // ul lists
      '/\n[0-9]+\.(.*)/' => '\2', // ol lists
      '/(&gt;|\>)+(.*)/' => '\2', // blockquotes
    );

    foreach ($rules as $regex => $replacement) {
      if (is_callable($replacement)) $text = preg_replace_callback($regex, $replacement, $text);
      else $text = preg_replace($regex, $replacement, $text);
    }

    $text = str_replace(".\n", '.', $text);
    $text = str_replace("\n", '.', $text);
    $text = str_replace('"', '', $text);

    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
  }

  /**
   * Clean text
   */
  public static function cleanText($content, $config)
  {
    $length = $config['plugins.tinyseo.description_length'];

    if ($length <= 1) $length = 20;

    $content = self::cleanMarkdown($content);

    // truncate the content to the number of words set in config
    $contentSmall = preg_replace('/((\w+\W*){' . $length . '}(\w+))(.*)/', '${1}', $content);

    // beware if content is less than length words, it will be nulled
    if ($contentSmall == '') $contentSmall = $content;

    return $contentSmall;
  }

  /**
   * Clean string
   */
  public static function cleanString($content)
  {
    $content = str_replace("&nbsp;", ' ', $content);
    $content = str_replace('"', "'", $content);
    $content = trim($content);

    return $content;
  }

  /**
   * Get page title
   */
  private function getPageTitle()
  {
    $page = $this->grav['page'];
    $header = $page->header();
    $defaultTitle = $this->cleanString($page->title());

    return isset($header->override_default_title) ? $header->override_default_title : $defaultTitle;

  }

  /**
   * Get page description
   */
  private function getPageDescription()
  {
    $config = $this->config;
    $page = $this->grav['page'];
    $header = $page->header();
    $content = substr(strip_tags($page->content()), 0, 1000);
    $defaultDescription = $this->cleanText($content, $config);

    return isset($header->override_default_desc) ? $header->override_default_desc : $defaultDescription;
  }

  /**
   * Get Image
   */
  private function getImage()
  {
    $config = $this->config;
    $page = $this->grav['page'];
    $header = $page->header();
    $image = null;

    if (!empty($page->value('media.image'))) {
      $images = $page->media()->images();

      if (isset($header->override_default_img)) {
        if (isset($images[$header->override_default_img])) {
          $page_image = $images[$header->override_default_img];
        }
      } else {
        $page_image = array_shift($images);
      }

      $image = $this->grav['uri']->base() . $page_image->url();

    } elseif (isset($config['plugins.tinyseo.backup_image'])) {
      $path = '/user/plugins/tinyseo/images/';
      $backup_image = $config['plugins.tinyseo.backup_image'];
      $image = $this->grav['uri']->base() . $path . $backup_image;
    }

    return $image;
  }

  /**
   * Get efault page title for admin blueprint
   */
  public static function defaultPageTitle()
  {
    $admin_page = Grav::instance()['admin']->page(true);
    $default_title = self::cleanString($admin_page->title());

    return $default_title;
  }

  /**
   * Get default page description for admin blueprint
   */
  public static function defaultPageDescription()
  {
    $config = Grav::instance()['config'];
    $admin_page = Grav::instance()['admin']->page(true);
    $header = $admin_page->header();
    $content = substr(strip_tags($admin_page->content()), 0, 1000);
    $default_description = self::cleanText($content, $config);

    return $default_description;
  }

  /**
   * Get default Image for admin blueprint
   */
  public static function defaultImage()
  {
    $admin_page = Grav::instance()['admin']->page(true);
    $default_image = null;

    if (!empty($admin_page->value('media.image'))) {
      $images = $admin_page->media()->images();
      $first_image = array_shift($images);
      $default_image = basename($first_image->url());
    }

    return $default_image;
  }
}
