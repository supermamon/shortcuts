# Media Mutt Changelog

* Media Mutt 2.1.1
  - (fix) crashing in share sheet

* Media Mutt 2.1.0
  - (new) Translations
  - (new) Open Downloads folder straight to the Files app
  - (new) Instagram TV downloads
  - (update) separate Tumblr downloader as an addon
  - (update) made some performance improvements
  - (fix) remove triple confirmation when resetting settings
  - (fix) error when there is media in the clipboard
  - (break) config file moved to `config` folder


* Media Mutt 2.0.6
  - (fix) Reddit videos being blank

* Media Mutt 2.0.5
  - fix) Tumblr download. Age-restricted urls still not supported.
  - (new) warn when there are multiple urls in the input

* Media Mutt 2.0.4
  - (update) Show a proper message when trying to download protected Youtube videos

* Media Mutt 2.0.3
  - (fix) Youtube age-restricted videos

* Media Mutt 2.0.2
  - (fix) Reddit title sometimes is not being used as filename
  - (fix) Reddit hosted video still fails for some videos

* Media Mutt 2.0.1
  - (fix) failure to identify Gfycat media url
  - (fix) failure to download HD Reddit hosted media

* Media Mutt 2.0.0
  - (fix) iOS 13 issues
  - (new) Use reddit post title as name
  - (fix) Download is not saved
  - (fix) Last update date not saved correctly

* Media Mutt 1.4.0
  - (new) Customizable update frequency
  - (new) Adaptive menu when update is available
  - (new) Settings menu items are now in a fixed order
  - (new) Add Back option to Downloads and Remove adddons
  - (update) Icons for all menus
  - (fix) not able to find media file on gfycat

* Media Mutt 1.3.1
  - Fix filename for Youtube downloads

* Go Fetch From Instagram Profile 1.1.0
  - Use new SaveTo setting from Media Mutt 1.3.0
  - Private profile detection
  - Previous download detection when Save To Files is on

* Media Mutt 1.3.0
  - New `Save To` setting
  - Remove `Always Download to Files` setting
  - Private profile detection for Instagram Post download
  - Add `Back` to Remove Addon menu
  - Single source for default settings


* 1.2.1
  - Clear clipboard after downloading to prevent repeated downloads when the `DownloadImmediately` setting is set to true.

* 1.2.0
  - Add a `getUserOpts` function. This can be used by addons to read user preferences
  - Removed Instagram User Profile url detection as it's not being handled
  - Default to overwrite existing media when saving

* 1.1.0
  - Add `Find Addons` item under Addons

* 1.0.1
  - Removed Contacts access requirement

* 1.0.0
  - Initial Release