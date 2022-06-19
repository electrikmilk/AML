# ArithMetic Language

### This is basically a programming language, but not really.

I am creating this project to practice creating a basic interpreter that has a lexer, that passes tokens to a parser,
that passes instructions to the interpreter to execute.

## Usage

Interactive shell:

```console
aml % php aml
Example: 2 + 2. Use "exit" or "q" to close.
> 2 + 2
4

> _
```

... or use a file:

```console
aml % php aml test.aml
4
4
10
0.5
0.3
0.3
```
