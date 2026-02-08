> [!WARNING]
> This documentation is currently under active construction.  
> Content may be incomplete, subject to change, or restructured as the Div engine evolves.  
> Please use with caution and check back regularly for updates.

# Overview

Requirements: PHP >= 8.0.

Div is a template engine and code generator written in PHP. It is designed to separate presentation concerns from data and behavior, and to support model-driven and generative workflows. The engine can adapt its syntax through dialects while preserving a canonical internal representation for parsing and transformation.

## Processing pipeline

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

## Convergence and recursion

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

## Dialects

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

## Loop scope and sub-instances

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

## Core operations

- Compile: combine a template with a model and save the result.
- Transform: convert one model into another by reusing compilation.
- Compose: assemble multiple outputs into a final artifact.

## Installation

```bash
composer require divengine/div
```

## Upgrade

```bash
composer upgrade
```

## Documentation map

- [[01 Introduction]]
- [[02 Template Features]]
- [[03 PHP Features]]
- [[04 Mechanisms]]
- [[05 Appendixes]]

See also [Release notes](../releases/README.md).

#templates
