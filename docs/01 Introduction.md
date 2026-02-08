# 1. Introduction

Div is a template engine that runs in PHP.

At its core, Div takes a template and a data model and produces text:

```php
echo new div($template, $data);
```

That is the fundamental contract. The output is always text.

Div is also used as a code generator and a data transformation tool because templates are not limited to HTML. A template can generate source code, configuration files, structured data, or even other templates. What Div generates can be reused as input for subsequent executions of the engine.

Div is the cornerstone of Divengine Software Solutions and has been developed continuously since 2011.

The template language is designed to be compact (minimal syntax for common operations), flexible (dialects allow alternative syntaxes), and descriptive (templates read as self-explanatory documents). Div assumes a clear division of concerns:

- The model specifies what data and rules are available.
- The template specifies the expected output structure.
- The engine provides the execution mechanism.

Capabilities span both template authoring and system integration:

- Variable replacement, formatting, modifiers, substring operations, and object property access within the provided model.
- Lists, iterations, conditional blocks, and other repetitive or branching constructs.
- Includes, inheritance, locations, and recursive processing until convergence.
- Formulas, macros, aggregate functions, and output cleanup (including HTML-to-text conversion).
- Configuration of defaults, globals, allowed functions, and ignored variables.
- Custom sub-parsers, hooks, logging, and use of a div instance as a string.

When applied consistently, this approach reduces repetitive work, enables reuse of models, supports multi-target outputs, and improves collaboration across stakeholders.
