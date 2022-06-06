# Classifier Plugin

***Abandonment Notice:** I'm afraid I simply don't have the time to maintain my Grav themes and plugins. Those interested in taking over should refer to the ["Abandoned Resource Protocol"](https://learn.getgrav.org/17/advanced/grav-development#abandoned-resource-protoc). Feel free to fork and replace. So long, and thanks for all the fish.*

The **Classifier** Plugin is for [Grav CMS](http://github.com/getgrav/grav). It allows you to add class declarations to specific HTML tags in a final rendered page.

## Installation

Installing the Classifier plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install classifier

This will install the Classifier plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/classifier`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `classifier`. You can find these files on [GitHub](https://github.com/aaron-dalton/grav-plugin-classifier) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/classifier
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/classifier/classifier.yaml` to `user/config/plugins/classifier.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
active: false
```

  * The `enabled` field lets you turn the plugin on or off.

  * The `active` field is usually left as `false` and only enabled in the frontmatter of the page you wish to affect.

## Usage

### Page

To activate the plugin, insert something like the following into the page's frontmatter:

```yaml
classifier:
    active: true
    tags:
        - tag: table
          nums: \*
          class: tinytext
```

  * The `active` field is what tells the plugin that you actually want to process the output of this page. Otherwise the plugin doesn't execute.

  * The `tags` field is where you specify what and which tags you wish to alter.

    * `tag` is the name of the HTML tag you want to find. It should be lowercase. In this example, you are looking for `<table>` tags.

    * `nums` tells the plugin which specific `<table>` tags you're looking for. Usually you would give a comma-delimited list of numbers (e.g., 1,2,4). In the example, we use `\*` to mean *all* `<table>` tags.

    * `class` is a string that will be inserted into the `class` attribute of the matching tags. It is inserted verbatim. The plugin does no checking that classes are not duplicated, misspelled, or malformed. It simply inserts the string.

You can also insert a `classifier` tag in your global `frontmatter.yaml` or put `tags` into the global config (`user/config/plugins/classifier.yaml`).

### CSS

If the class already exists in the loaded CSS, then great, but the plugin will also check for a dedicated CSS file and load it if found: `theme://assets/classifier.css`. 

