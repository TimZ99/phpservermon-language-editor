# PHP Server Monitor Language Editor (PSMLE)
[![Build Status](https://travis-ci.org/TimZ99/phpservermon-language-editor.svg?branch=master)](https://travis-ci.org/TimZ99/phpservermon-language-editor)

Helps to maintain the translations files of PHP Server Monitor. PHP Server Monitor is translated into more than 20 languages. This is great, but it makes it way harder to keep all of the translations up to date. This script provides a clear view of the values that not yet have a translation in an effort to keep all the translations up to date!

The default language of PHP Server Monitor is English. All of the translations are compared against the default language. If the border of the input field is red it means that the value is not yet translated. If the border is orange, then the translation is the same as the default. In some cases this can be ignored, in other cases, the value needs a translation.

## Getting Started

- Make sure that the file you're going to edit is writable. Permission of at least 0666 is required. 
- Open the file in your browser, start editing and save your work.
- Share your work [here](https://github.com/phpservermon/phpservermon/pull).

It isn't really more than that!

**Friendly reminders:**
- Change the permission of your language files back to 0644 after you're done.
- User input is used unfiltered! It's *not* recommended to keep this file accessible for everyone.


### Prerequisites

As this project is specifically made to maintain the translation files of PHP Server Monitor, you need PHP Server Monitor. https://github.com/phpservermon/phpservermon

As this project uses PHP, you need PHP installed.

### Installing

- Clone the repository to a server running PHP.
- In trans.php:
   - Change $path to your PHP Server Monitor language folder.
   - Change $translationLang to the file you want to edit.

## Development
### Running the tests

- Run ```composer install --dev```.
- Run ```./vendor/bin/phpcs -p trans.php --standard=PHPCompatibility --runtime-set testVersion 5.6```
Change 5.6 to the version you want to test on.

## Authors

* **Tim Zandbergen** - *Initial work* - [TimZ99](https://github.com/TimZ99)

See also the list of [contributors](https://github.com/TimZ99/phpservermon-language-editor/contributors) who participated in this project.

## License

This project is licensed under the GNU GPL v3 License - see the [LICENSE.md](LICENSE.md) file for details.
