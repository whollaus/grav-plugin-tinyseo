# Tiny SEO Plugin 🏆

**Tiny SEO** is a plugin for [Grav CMS](http://github.com/getgrav/grav) that provide a simple way to manage SEO from admin.

<p align="center">
  <img width="480" height="301" src="https://media.giphy.com/media/3o7TKJhBfNCiispgDm/giphy.gif">
  <br>
  Get your website to the top of search engine!!!
</p>


## 📦 Installation

Installing the Tiny SEO plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a tiny terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The tinyst way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line). From the root of your Grav install type:

    bin/gpm install tinyseo

This will install the Tiny SEO plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/tinyseo`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `tinyseo`. You can find these files on [GitHub](https://github.com/jimblue/grav-plugin-tinyseo) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/tinyseo

> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

### Admin Plugin

If you use the admin plugin, you can install directly through the admin plugin by browsing the `Plugins` tab and clicking on the `Add` button.

## 📐 Configuration

Before configuring this plugin, you should copy the `user/plugins/tinyseo/tinyseo.yaml` to `user/config/plugins/tinyseo.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
meta_robots:
  index: true
  follow: true
  noindex: false
  nofollow: false
  noimageindex: false
description_length: 200
truncate_break: world
twitter_card_type: summary_large_image
```

## ✨ Usage

### Default settings

You just have to enable the plugin and configure your default settings to auto generate seo tags.
You can see the result by looking at the source in the browser.

#### Robots directives

Robots meta directives (sometimes called “meta tags”) are pieces of code that provide crawlers instructions for how to crawl or index web page content. Whereas robots.txt file directives give bots suggestions for how to crawl a website's pages, robots meta directives provide more firm instructions on how to crawl and index a page's content.

You can choose between 5 mode:

* **index**: _Tells a search engine to index a page. Note that you don’t need to add this meta tag; it’s the default._
* **follow**: _Even if the page isn’t indexed, the crawler should follow all the links on a page and pass equity to the linked pages._
* **noindex**: _Tells a search engine not to index a page._
* **nofollow**: _Tells a crawler not to follow any links on a page or pass along any link equity._
* **noimageindex**: _Tells a crawler not to index any images on a page._

#### Description Length

The default description lengh is used to auto generate meta description for a page.

#### Truncate Break

Truncate break define how auto generate meta description should be truncate after description length.

You can choose between 3 mode:

* **at character**: _break at description length exactly. word may be broken at any character_
* **at the end of word**: _break at description length to the closest word end. prevent word break_
* **at the end of sentence**: _break at description length to the closest sentence end. prevent sentence break_

#### Default image

The default image is used as a callback if the page doesn't contain any image.

#### Site title

The site title is used to auto generate meta title.

#### Twitter card Type

The twitter card design to use when a page is shared.

You can choose between 2 mode:

* **summary**: _The Summary Card can be used for many kinds of web content, from blog posts and news articles, to products and restaurants. It is designed to give the reader a preview of the content before clicking through to your website._
* **summary large image**: _The Summary Card with Large Image features a large, full-width prominent image alongside a tweet. It is designed to give the reader a rich photo experience, and clicking on the image brings the user to your website._

#### Twitter ID

Your **twiter username** is used to add twitter meta.

#### Facebook App ID

Your **facebook app ID** is used to add twitter meta.

### Override default settings on specific page

If Tiny Seo is correctly configured and enabled you will see a new SEO tab when editing a page in the admin.

In this tab each field have a placeholder that reflect the actual default value generated by Tiny Seo.

To override the default value simply fill the corresponding field.

To get default value back simply remove you input.
