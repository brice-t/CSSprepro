What is CSSprepro ?
==============================

CSSprepro is a plugin for [Jelix](http://jelix.org) PHP framework. It allows you to use easily dynamic stylesheet languages (or, more generaly, preprocess CSS files) in Jelix using sub-plugins.

This is an htmlresponse plugin.



Installation
============

Under Jelix default configuration, create an "htmlresponse" directory (if missing) in your project's "plugins" directory.
Clone this repository in that directory with :

    git clone git@github.com:brice-t/CSSprepro.git


Note that you should have your app plugin directory in your modulesPath (defaultconfig.ini.php or entry point's config.ini.php) to get it working.
The value should be at least :

    modulesPath="app:modules/"



Usage
=====

Your config file must activate CSSprepro plugin :

    [jResponseHtml]
    plugins=CSSprepro

As itself, this plugin has no effect. You should add at least one sub-plugin and configure CSSprepro with _CSSprepro\_plugins_ to use it. E.g. :

    [jResponseHtml]
    CSSprepro_plugins[]=phpsass

N.B. : the directories containing pre-processed CSS files should be writable by your web server ! Indeed, compiled files will be written in that very same directory so that relative urls go on working ...




Config
======

You can configure CSSprepro's behviour regarding compilation:

    [jResponseHtml]
    ;...
    ; always|onchange|once
    CSSprepro_compile=always

If CSSprepro\_compile's value is not valid or empty, its default value is onchange.

* always : pre-process CSS file on all requests
* onchange : pre-process CSS file only if it has changed
* once : pre-process CSS file once and never compile it again (until compiled file is removed)


Note that for each sub-plugin, you can override this configuration. E.g. for "phpsass" sub-plugin you can set :

    [jResponseHtml]
    ;...
    ; always|onchange|once
    CSSprepro_phpsass_compile=always



