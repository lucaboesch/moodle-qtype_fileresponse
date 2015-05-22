# File Response

File Response is a Moodle question type that provides some additional functions compared to the essay question type, which can as well be used to prompt for files in a question setting.

## Features

* Possibility to prevent "Save as…" OS dialogs where participants could browse the file system and possibly open executables by using the [menu key][menukey], circumventing the suppressed mouse context-click (important in a [Safe Exam Browser][safeexambrowser] environment).
* Allow or disallow attachment downloads when there are uploaded files, individually for each question.
* Allow or disallow repositories when uploading files, individually for each question.

## Download

Visit [File Response's Github page][fileresponse_github] to either download a package or clone the git repository.

## Installation

File Response question type should be installed as 'fileresponse' to your moodle/question/type directory.

## Contributions

Contributions of any form are welcome. Github pull requests are preferred.

File any bugs, improvements, or feature requiests in our [issue tracker][issues].

Possible issues are likely to be found in the backup / restore / migrate question from a Moodle version to another process, since there has not been excessive testing in those fields.

## License

File Response adopts the same license that Moodle does.

[fileresponse_github]: https://github.com/bfh/moodle-qtype_fileresponse
[issues]: https://github.com/bfh/moodle-qtype_fileresponse/issues
[safeexambrowser]: http://www.safeexambrowser.org
[menukey]: http://en.wikipedia.org/wiki/Menu_key

