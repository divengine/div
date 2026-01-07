Div parse the template language locating some syntax structures that have rules. In this section the structures and their rules are explained.

#### Rigid blocks
![[Pasted image 20240128115015.png]]

|   |   |   |
|---|---|---|
|PREFIX|RIGID SYNTAX|SUFFIX|

The rigid blocks are those that are compound for a rigid syntaxis with prefix and a suffix. The rigid syntax is that in the one that each character has a meaning for the interpreter, in such way that characters are not allowed of more. That is to say, characters like the space (chr(32)), the tabs (\t) and the new lines (\n), that allow to beautify the code, won't be ignored by the interpreter and it will consider them like part of their syntactic analysis. In this blocks, the prefix and the suffix are required.

For example, if you write **"{$text }"** the name of the variable to substitute will include the space at the end, that is to say, it will be **"text "**.

Example of rigid blocks are [[Simple replacements]] and [[Including another templates]].

#### Simple blocks

|   |   |   |
|---|---|---|
|BEGIN|FLEXIBLE SYNTAX|END|

The simple blocks are similar to the rigid blocks, with the difference that the prefix is named "begin", the suffix is named "end", and the syntaxis of the block is flexible. A flexible syntax is the opposite to a rigid syntax, where it is allowed to write characters to space or to format. In this blocks, the aperture tag and the closing tag are required.

Example of simple blocks are [[Ignored parts (escaping Div parsing)]], [[Comments]] and [[Strip or clean the resulting code]]
#### No-keyword blocks

|   |   |   |
|---|---|---|
|BEGIN_PREFIX|FLEXIBLE SYNTAX|BEGIN_SUFFIX|
|ANY CODE + SPECIAL TAGS|   |   |
|END|

The blocks without keywords are those where the aperture tag and the closing tag have a structure or specific content, so that it is not necessary a prefix and a suffix for the closing tag. In this sense the prefix and the suffix of the aperture tag are required, and as well as the closing tag.

Example of no-keyword blocks are [[Conditions]] and [[Iterations]]

#### Keyword blocks

|   |   |   |
|---|---|---|
|BEGIN_PREFIX|KEYWORD|BEGIN_SUFFIX|
|ANY CODE + SPECIAL TAGS|   |   |
|END_PREFIX|KEYWORD|END_SUFFIX|

The blocks with keywords are those where the aperture tag and the closing tag contain the name a variable, either simple or complex, accompanied by a prefix and a suffix in both cases. The prefix of the aperture tag and the suffix of the closing tag are required.

Example of keyword blocks are [[Conditional parts]] and [[Lists (loops)]]
