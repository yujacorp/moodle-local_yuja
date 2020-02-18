A set of [Moodle](http://moodle.org) plugins that integrate with [YuJa](http://www.yuja.com).

Designed to work with Moodle 2.4+.

## Overview ##
These plugins allow for the integration of YuJa with the Moodle platform with the ability
to insert YuJa media using a button integrated with your text editor of choice

## YuJa Moodle Plugins ##

**Local** `(local/yuja)`

* Allows for LTI configuration and provides lti integration for the other plugins.

**Atto** `(lib/editor/atto/plugins/yuja)`

* Allows you to insert YuJa media using the Atto editor

**TinyMce** `(lib/editor/tinymce/plugins/yuja)`

* Allows you to insert YuJa media using the TinyMCE editor

## Plugin Installation/Upgrade ##

If you have one zip for each plugin containing a root folder named `yuja` then you can
install each plugin by `Site administration -> Plugins -> Install Plugins` and providing
the zip file in turn and clicking `Install plugin from the ZIP file`, one plugin at a time.

If you have one zip for all the plugins, or wish to perform the installation manually,
you can do so by copying the `yuja` folder from each of the below locations into the 
corresponding folders on your moodle server

* From `yuja_local` copy the `yuja` folder to `path/to/your/moodle/local/`

#### Moodle 2.4+: ####
* From `tinymce_yuja` copy the `yuja` folder to `path/to/your/moodle/lib/editor/tinymce/plugins/`

#### Moodle 2.7+: ####
* From `atto_yuja` copy the `yuja` folder to `path/to/your/moodle/lib/editor/atto/plugins/`

To finalize the installation you will need to login as an administrator to your moodle and 
navigate to: `Site administration -> Notifications`.  You should see the modified plugins listed, 
(If not, click "Check for available updates")  Once you see the modified plugins listed, 
click "Upgrade Moodle database now" to complete the installation.

If this is your first time installing the local YuJa plugin, you will be asked to enter your
configuration information.  The configuration details will match those used to configure
the YuJa External tool.  Please see the corresponding documentation for details.

If this is your first time installing the Atto plugin, note that it requires a small amount of
configuration in addition to that required for the local plugin.  See below

## Base Plugins Configuration ##

### Local Plugin Configuration ###

The settings dialog for the Local Plugin will be shown immediately after installing it.  If not,
or to access it later, navigate to `Site administration -> Plugins -> Local plugins -> YuJa Package Config`.

You will be asked to enter your YuJa access url, consumer key, and shared secret.  These will 
have been provided to you by your YuJa Solutions Engineer and are the same details you use to
configure an external tool to access the YuJa site.

## Text Editor Plugins Configuration ##

### Atto Rich text editor plugin config: ###

Once installed, the Atto plugin requires a little bit of additional configuration

* Go to `http://your.moodle/admin/settings.php?section=editorsettingsatto` and add a new line in the
Toolbar config text area containing `yuja = yuja` where you want the button to appear. 
See [Text Editor Toolbar Settings](http://docs.moodle.org/27/en/Text_editor#Toolbar_settings) on how to 
fine tune Atto's toolbar to your liking.

### TinyMCE Rich text editor plugin config: ###

* Once the plugin has been installed (see above), configuration is automatic.
