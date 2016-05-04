(this is a work in progress)

1. Code formatting MUST follow PSR-2.
1. Issues SHOULD include code and/or data to reproduce the issue.
1. PR's for issues SHOULD include test(s) for issue.
1. PR's SHOULD have adequate documentation (commit messages, comments, etc.) to readily convey what and/or why.
1. Code SHOULD attempt to follow [Object Calisthenics](http://www.xpteam.com/jeff/writings/objectcalisthenics.rtf) methodology.

## Exceptions
1. All exceptions thrown MUST implement ```CrEOF\Geo\Obj\Exception\ExceptionInterface```.
1. All thrown exception messages MUST end with a period "."
1. Without a compelling reason new exception classes SHOULD have a use scope outside a single class.
