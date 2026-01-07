> [!WARNING]
> This documentation is currently under active construction.  
> Content may be incomplete, subject to change, or restructured as the Div engine evolves.  
> Please use with caution and check back regularly for updates.

**div** is a [template engine](https://en.wikipedia.org/wiki/Template_processor) and [code generator tool](https://en.wikipedia.org/wiki/Code_generation_%28compiler%29) tool written in [PHP](http://php.net/) and developed since 2011, designed to optimize collaboration between developers and designers through generative programming, model-driven architecture, and meta-programming. This engine not only facilitates the separation of labor between roles but also allows for deep customization through the creation of tailored template [dialects](https://dialector.divengine.org) to meet specific project needs.

```mermaid
flowchart TD
    A["Input: Template (text)"] --> B["Dialector: Normalize to canonical Div"]
    B --> C["Div instance (src, items, config)"]
    C --> D["Parsing pass: Structure detection"]

    subgraph T["Transformers (per phase)"]
      T0["(group)"]
      T1["Includes"]
      T2["Conditions"]
      T3["Loops / Lists"]
      T4["Replacements / Variables"]
      T5["Modifiers"]
      T6["Macros / Formulas"]
      T7["Capsules / Strip / Ignore / Comment"]
      T0 --> T1
      T0 --> T2
      T0 --> T3
      T0 --> T4
      T0 --> T5
      T0 --> T6
      T0 --> T7
    end

    D --> T0
    T1 --> E["Transformed text"]
    T2 --> E
    T3 --> E
    T4 --> E
    T5 --> E
    T6 --> E
    T7 --> E
    E --> F{"Did it change? (CRC)"}
    F -- Yes --> D
    F -- No --> G["Output: Final text (HTML/code/etc.)"]
```

One of the most distinctive features of **div** is its ability to **recursively process templates until there is no more code to process**, effectively avoiding infinite loops and enabling complex, multi-step transformations. This translates into exceptional flexibility for dynamically generating content or code based on the data and logic specified in the templates.

```mermaid
flowchart LR
    A["Init parse()"] --> B["crc_prev = crc32(src)"]
    B --> C["Apply phase: detect and transform blocks"]
    C --> D["crc_now = crc32(src)"]
    D --> E{"crc_now != crc_prev?"}
    E -- "Yes" --> F["cycles = cycles + 1"]
    F --> G{"cycles > MAX?"}
    G -- "Yes" --> H["Abort with error"]
    G -- "No" --> B
    E -- "No" --> I["Convergence reached: return src"]
```

Additionally, **div** supports the creation of custom template dialects, allowing users to define and modify the syntax to better suit different programming environments or to enhance code readability and maintenance. For example, it's possible to configure a dialect that ensures templates remain as valid XML, facilitating integration with other systems and technologies that utilize XML.

```mermaid
flowchart LR
    A["Input: Template (Dialect syntax)"] --> C["Dialector: Normalize to canonical Div"]

    B1["Dialect config: mapping tables"] --> C
    B2["Delimiters & keywords"] --> C
    B3["Tags & block markers"] --> C
    B4["Modifiers & separators"] --> C
    B5["Escape rules & collision handling"] --> C

    C --> D["Canonical Div (text)"]
    D --> E["Div instance (src, items, config)"]
    E --> F["Parsing pass: structure detection"]
    F --> G{"Did it change? (CRC)"}
    G -- "Yes" --> F
    G -- "No" --> H["Final output (HTML/code/etc.)"]
```

## Scopes and sub-instances (loop render parallel/serial)

```mermaid
sequenceDiagram
    participant E as Engine (div)
    participant T as Template (list block)
    participant D as Data (items[])
    Note over E,T: E detects a list block [ $ items ]
    E->>T: Extract inner block template
    loop For each item in items
        E->>E: Create sub-instance div (isolated scope)
        E->>E: Set items = item (shadowing/merge)
        E->>E: parse() (local multipass)
        E-->>E: Partial result (fragment)
    end
    E->>E: Concatenate fragments in order
    E-->>T: Replace block with concatenation
    E-->>E: Continue global multipass parse
```

This engine is the cornerstone of [Divengine Software Solutions](https://divengine.com) and adheres to the philosophy of *"build more with less"* and *"divide the problem, not the people."* **div** proposes code generation based on templates that adhere to clear rules: the model contains all information about what is to be accomplished; the templates define the expected outcomes; and the engine, acting as a black box, takes care of the execution.

Basic operations include:

- **Compile**: Combine a template with models and save the result.
- **Transform**: Convert one model to another, reusing the compile operation.
- **Compose**: Integrate different results using the engine and other tools.

```mermaid
classDiagram
    class Block {
      +delimiters
      +rules()
      +examples()
    }
    class RigidBlock {
      +prefix
      +suffix
      +whitespace: significant
    }
    class SimpleBlock {
      +begin
      +end
      +whitespace: flexible
    }
    class KeywordBlock {
      +begin_prefix
      +keyword
      +begin_suffix
      +end_prefix
      +keyword
      +end_suffix
    }
    class NoKeywordBlock {
      +begin_prefix
      +begin_suffix
      +end
      +closing without repeating keyword
    }
    Block <|-- RigidBlock
    Block <|-- SimpleBlock
    Block <|-- KeywordBlock
    Block <|-- NoKeywordBlock

```

With **div**, developers and designers can avoid repetitive tasks, scale projects based on models, migrate projects to different technologies, and expand applications to other platforms and devices, all while improving application performance and enabling non-technical people to participate in the project's development.

## Install

```bash
composer require divengine/div
```
## Upgrade

```bash
composer upgrade
```

[![Readme Card](https://github-readme-stats.vercel.app/api/pin/?username=divengine&repo=div&show_owner=true&rand=23)](https://github.com/anuraghazra/github-readme-stats)

[[Introduction to Div PHP Template Engine]]
[[The div class]]
[[The best practices]]
[[Template Engine Features]]
[[Method's reference]]
[[Mechanisms]]
[[Appendixes]]

Se also the [[CHANGELOG]] and the [[FUTURE]] or this project.


#templates
