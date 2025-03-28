<?php

const KEYWORDS = [
    'break',
    'const',
    'continue',
    'echo',
    'else',
    'elseif',
    'for',
    'function',
    'if',
    'return',
    'throw',
    'while',
];

function tokenize(string $src): array
{
    $tokens = [];
    $pos = 0;
    $len = strlen($src);

    while ($pos < $len) {
        $c = $src[$pos];
        ++$pos;
        if (ctype_space($c)) {
            while ($pos < $len && ctype_space($src[$pos])) {
                ++$pos;
            }
        } elseif ($c === '+') {
            if ($src[$pos] === '=') {
                ++$pos;
                $tokens[] = ['+=', null];
            } elseif ($src[$pos] === '+') {
                ++$pos;
                $tokens[] = ['++', null];
            } else {
                $tokens[] = ['+', null];
            }
        } elseif ($c === '-') {
            if ($src[$pos] === '=') {
                ++$pos;
                $tokens[] = ['-=', null];
            } elseif ($src[$pos] === '-') {
                ++$pos;
                $tokens[] = ['--', null];
            } else {
                $tokens[] = ['-', null];
            }
        } elseif ($c === '&') {
            if ($src[$pos] === '&') {
                ++$pos;
                $tokens[] = ['&&', null];
            } else {
                $tokens[] = ['&', null];
            }
        } elseif ($c === '*') {
            if ($src[$pos] === '=') {
                ++$pos;
                $tokens[] = ['=', null];
            } else {
                $tokens[] = ['*', null];
            }
        } elseif ($c === '{') {
            $tokens[] = ['{', null];
        } elseif ($c === '}') {
            $tokens[] = ['}', null];
        } elseif ($c === '(') {
            $tokens[] = ['(', null];
        } elseif ($c === ')') {
            $tokens[] = [')', null];
        } elseif ($c === '[') {
            $tokens[] = ['[', null];
        } elseif ($c === ']') {
            $tokens[] = [']', null];
        } elseif ($c === ':') {
            $tokens[] = [':', null];
        } elseif ($c === ';') {
            $tokens[] = [';', null];
        } elseif ($c === ',') {
            $tokens[] = [',', null];
        } elseif ($c === '.') {
            if ($src[$pos] === '=') {
                ++$pos;
                $tokens[] = ['.=', null];
            } else {
                $tokens[] = ['.', null];
            }
        } elseif ($c === '=') {
            if ($src[$pos] === '=') {
                ++$pos;
                if ($src[$pos] === '=') {
                    ++$pos;
                    $tokens[] = ['===', null];
                } else {
                    $tokens[] = ['==', null];
                }
            } else {
                $tokens[] = ['=', null];
            }
        } elseif ($c === '!') {
            if ($src[$pos] === '=') {
                ++$pos;
                if ($src[$pos] === '=') {
                    ++$pos;
                    $tokens[] = ['!==', null];
                } else {
                    $tokens[] = ['!=', null];
                }
            } else {
                $tokens[] = ['!', null];
            }
        } elseif ($c === '<') {
            if ($src[$pos] === '?') {
                ++$pos;
                if ($src[$pos] === 'p') {
                    ++$pos;
                    if ($src[$pos] === 'h') {
                        ++$pos;
                        if ($src[$pos] === 'p') {
                            ++$pos;
                            $tokens[] = ['<?php', null];
                        } else {
                            throw new \RuntimeException('invalid char');
                        }
                    } else {
                        throw new \RuntimeException('invalid char');
                    }
                } else {
                    throw new \RuntimeException('invalid char');
                }
            } elseif ($src[$pos] === '=') {
                ++$pos;
                $tokens[] = ['<=', null];
            } else {
                $tokens[] = ['<', null];
            }
        } elseif ($c === '>') {
            if ($src[$pos] === '=') {
                ++$pos;
                $tokens[] = ['>=', null];
            } else {
                $tokens[] = ['>', null];
            }
        } elseif ($c === '%') {
            if ($src[$pos] === '=') {
                ++$pos;
                $tokens[] = ['%=', null];
            } else {
                $tokens[] = ['%', null];
            }
        } elseif ($c === '\\') {
            $tokens[] = ['\\', null];
        } elseif ($c === '|') {
            if ($src[$pos] === '|') {
                ++$pos;
                $tokens[] = ['||', null];
            } else {
                $tokens[] = ['|', null];
            }
        } elseif ($c === '/') {
            if ($src[$pos] === '/') {
                ++$pos;
                while ($pos < $len && $src[$pos] !== "\n") {
                    ++$pos;
                }
            } elseif ($src[$pos] === '*') {
                ++$pos;
                while ($pos < $len && ! ($src[$pos] === '*' && $src[$pos + 1] === '/')) {
                    ++$pos;
                }
                $pos += 2;
            } elseif ($src[$pos] === '=') {
                ++$pos;
                $tokens[] = ['/=', null];
            } else {
                $tokens[] = ['/', null];
            }
        } elseif ($c === '"') {
            $value = '';
            while ($pos < $len && $src[$pos] !== '"') {
                if ($src[$pos] === '\\') {
                    ++$pos;
                    if ($pos === $len) {
                        throw new \RuntimeException('invalid string');
                    }
                    $escape = $src[$pos];
                    if ($escape === 'n') {
                        $value .= "\n";
                    } elseif ($escape === 'r') {
                        $value .= "\r";
                    } elseif ($escape === 't') {
                        $value .= "\t";
                    } elseif ($escape === '\\') {
                        $value .= '\\';
                    } else {
                        $value .= $escape;
                    }
                    ++$pos;
                } else {
                    $value .= $src[$pos];
                    ++$pos;
                }
            }
            if ($src[$pos] !== '"') {
                throw new \RuntimeException('invalid string');
            }
            ++$pos;
            $tokens[] = ['constant_encapsed_string', $value];
        } elseif ($c === "'") {
            $value = '';
            while ($pos < $len && $src[$pos] !== "'") {
                if ($src[$pos] === '\\') {
                    ++$pos;
                    if ($pos === $len) {
                        throw new \RuntimeException('invalid string');
                    }
                    $escape = $src[$pos];
                    if ($escape === 'n') {
                        $value .= "\n";
                    } elseif ($escape === 'r') {
                        $value .= "\r";
                    } elseif ($escape === 't') {
                        $value .= "\t";
                    } elseif ($escape === '\\') {
                        $value .= '\\';
                    } else {
                        $value .= $escape;
                    }
                    ++$pos;
                } else {
                    $value .= $src[$pos];
                    ++$pos;
                }
            }
            if ($src[$pos] !== "'") {
                throw new \RuntimeException('invalid string');
            }
            ++$pos;
            $tokens[] = ['constant_encapsed_string', $value];
        } elseif ($c === '$') {
            $name = '';
            while ($pos < $len && (ctype_alnum($src[$pos]) || $src[$pos] === '_')) {
                $name .= $src[$pos];
                ++$pos;
            }
            if ($name === '') {
                throw new \RuntimeException('invalid var');
            }
            $tokens[] = ['variable', $name];
        } elseif (ctype_digit($c)) {
            $value = $c;
            while ($pos < $len && ctype_digit($src[$pos])) {
                $value .= $src[$pos];
                ++$pos;
            }
            $tokens[] = ['lnumber', $value];
        } elseif (ctype_alpha($c) || $c === '_') {
            $name = $c;
            while ($pos < $len && (ctype_alnum($src[$pos]) || $src[$pos] === '_')) {
                $name .= $src[$pos];
                ++$pos;
            }
            if (in_array(strtolower($name), KEYWORDS)) {
                $tokens[] = [strtolower($name), null];
            } else {
                $tokens[] = ['string', $name];
            }
        } else {
            throw new \RuntimeException("invalid char: {$c}");
        }
    }
    return $tokens;
}

function parse(array $tokens): array
{
    expect_token($tokens, 0, '<?php');
    return parse_statements($tokens, 1, '')[0];
}

function parse_statements(array $tokens, int $pos, string $delimiter): array
{
    $statements = [];
    while ($pos < count($tokens) && $tokens[$pos][0] !== $delimiter) {
        [$statement, $pos] = parse_statement($tokens, $pos);
        $statements[] = $statement;
    }
    return [['statements', $statements], $pos];
}

function parse_statement(array $tokens, int $pos): array
{
    $t = $tokens[$pos];
    if ($t[0] === 'const') {
        return parse_const_declaration($tokens, $pos);
    } elseif ($t[0] === 'function') {
        return parse_function_declaration($tokens, $pos);
    } elseif ($t[0] === 'while') {
        return parse_while_statement($tokens, $pos);
    } elseif ($t[0] === 'for') {
        return parse_for_statement($tokens, $pos);
    } elseif ($t[0] === 'if') {
        return parse_if_statement($tokens, $pos);
    } elseif ($t[0] === 'return') {
        return parse_return_statement($tokens, $pos);
    } elseif ($t[0] === 'break') {
        return parse_break_statement($tokens, $pos);
    } elseif ($t[0] === 'continue') {
        return parse_continue_statement($tokens, $pos);
    } elseif ($t[0] === 'echo') {
        return parse_echo_statement($tokens, $pos);
    } elseif ($t[0] === 'throw') {
        return parse_throw_statement($tokens, $pos);
    }
    return parse_expression_statement($tokens, $pos);
}

function parse_const_declaration(array $tokens, int $pos): array
{
    $pos++; // const
    $name = $tokens[$pos][1];
    $pos++; // <name>
    $pos++; // =
    [$expr, $pos] = parse_expression($tokens, $pos);
    $pos++; // ;
    return [['const', $name, $expr], $pos];
}

function parse_function_declaration(array $tokens, int $pos): array
{
    $pos++; // function
    $name = $tokens[$pos][1];
    expect_token($tokens, $pos, 'string');
    $pos++; // <name>
    expect_token($tokens, $pos, '(');
    $pos++; // (
    [$parameters, $pos] = parse_parameters($tokens, $pos);
    expect_token($tokens, $pos, ')');
    $pos++; // )
    if ($tokens[$pos][0] === ':') {
        $pos++; // :
        $pos++; // <type>
    }
    expect_token($tokens, $pos, '{');
    $pos++; // {
    [$statements, $pos] = parse_statements($tokens, $pos, '}');
    expect_token($tokens, $pos, '}');
    $pos++; // }
    return [['function', $name, $parameters, $statements], $pos];
}

function parse_parameters(array $tokens, int $pos): array
{
    $parameters = [];
    while (true) {
        if ($tokens[$pos][0] === ')') {
            break;
        }
        if ($tokens[$pos][0] === 'string') {
            ++$pos;
        }
        expect_token($tokens, $pos, 'variable');
        $parameters[] = $tokens[$pos][1];
        ++$pos;
        if ($tokens[$pos][0] === ',') {
            ++$pos;
        } elseif ($tokens[$pos][0] === ')') {
            break;
        } else {
            throw new \RuntimeException("invalid token: {$tokens[$pos][0]}");
        }
    }
    return [$parameters, $pos];
}

function parse_while_statement(array $tokens, int $pos): array
{
    expect_token($tokens, $pos, 'while');
    ++$pos; // while
    expect_token($tokens, $pos, '(');
    ++$pos; // (
    [$cond, $pos] = parse_expression($tokens, $pos);
    expect_token($tokens, $pos, ')');
    ++$pos; // )
    expect_token($tokens, $pos, '{');
    ++$pos; // {
    [$body, $pos] = parse_statements($tokens, $pos, '}');
    expect_token($tokens, $pos, '}');
    ++$pos; // }
    return [['while', $cond, $body], $pos];
}

function parse_for_statement(array $tokens, int $pos): array
{
    expect_token($tokens, $pos, 'for');
    ++$pos; // for
    expect_token($tokens, $pos, '(');
    ++$pos; // (
    [$init, $pos] = parse_expression($tokens, $pos);
    expect_token($tokens, $pos, ';');
    ++$pos; // ;
    [$cond, $pos] = parse_expression($tokens, $pos);
    expect_token($tokens, $pos, ';');
    ++$pos; // ;
    [$update, $pos] = parse_expression($tokens, $pos);
    expect_token($tokens, $pos, ')');
    ++$pos; // )
    expect_token($tokens, $pos, '{');
    ++$pos; // {
    [$body, $pos] = parse_statements($tokens, $pos, '}');
    expect_token($tokens, $pos, '}');
    ++$pos; // }
    return [['for', $init, $cond, $update, $body], $pos];
}

function parse_if_statement(array $tokens, int $pos): array
{
    ++$pos; // if or elseif
    expect_token($tokens, $pos, '(');
    ++$pos; // (
    [$cond, $pos] = parse_expression($tokens, $pos);
    expect_token($tokens, $pos, ')');
    ++$pos; // )
    expect_token($tokens, $pos, '{');
    ++$pos; // {
    [$then, $pos] = parse_statements($tokens, $pos, '}');
    expect_token($tokens, $pos, '}');
    ++$pos; // }
    if ($tokens[$pos][0] === 'elseif') {
        [$else, $pos] = parse_if_statement($tokens, $pos);
    } elseif ($tokens[$pos][0] === 'else') {
        expect_token($tokens, $pos, 'else');
        ++$pos; // else
        expect_token($tokens, $pos, '{');
        ++$pos; // {
        [$else, $pos] = parse_statements($tokens, $pos, '}');
        expect_token($tokens, $pos, '}');
        ++$pos; // }
    } else {
        $else = null;
    }
    return [['if', $cond, $then, $else], $pos];
}

function parse_return_statement(array $tokens, int $pos): array
{
    ++$pos; // return
    if ($tokens[$pos][0] === ';') {
        $ret = null;
    } else {
        [$ret, $pos] = parse_expression($tokens, $pos);
    }
    expect_token($tokens, $pos, ';');
    ++$pos; // ;
    return [['return', $ret], $pos];
}

function parse_break_statement(array $tokens, int $pos): array
{
    $pos += 2;
    return [['break'], $pos];
}

function parse_continue_statement(array $tokens, int $pos): array
{
    $pos += 2;
    return [['continue'], $pos];
}

function parse_echo_statement(array $tokens, int $pos): array
{
    ++$pos; // echo
    [$expr, $pos] = parse_expression($tokens, $pos);
    ++$pos; // ;
    return [['echo', $expr], $pos];
}

function parse_throw_statement(array $tokens, int $pos): array
{
    ++$pos; // throw
    ++$pos; // new
    ++$pos; // \
    ++$pos; // <name>
    ++$pos; // (
    [$args, $pos] = parse_arguments($tokens, $pos);
    ++$pos; // )
    expect_token($tokens, $pos, ';');
    ++$pos; // ;
    return [['throw'], $pos];
}

function parse_expression_statement(array $tokens, int $pos): array
{
    [$expr, $pos] = parse_expression($tokens, $pos);
    $pos++; // ;
    return [['expression', $expr], $pos];
}

function parse_expression(array $tokens, int $pos): array
{
    return parse_assign_expression($tokens, $pos);
}

function parse_assign_expression(array $tokens, int $pos): array
{
    [$lhs, $pos] = parse_boolean_or_expression($tokens, $pos);
    $op = $tokens[$pos][0];
    if (in_array($op, ['=', '+=', '-=', '.='])) {
        ++$pos;
        [$rhs, $pos] = parse_assign_expression($tokens, $pos);
        return [['assign', $op, $lhs, $rhs], $pos];
    }
    return [$lhs, $pos];

}

function parse_boolean_or_expression(array $tokens, int $pos): array
{
    [$lhs, $pos] = parse_boolean_and_expression($tokens, $pos);
    $op = $tokens[$pos][0];
    if ($op === '||') {
        ++$pos;
        [$rhs, $pos] = parse_boolean_or_expression($tokens, $pos);
        return [['infix', $op, $lhs, $rhs], $pos];
    }
    return [$lhs, $pos];

}

function parse_boolean_and_expression(array $tokens, int $pos): array
{
    [$lhs, $pos] = parse_equality_expression($tokens, $pos);
    $op = $tokens[$pos][0];
    if ($op === '&&') {
        ++$pos;
        [$rhs, $pos] = parse_boolean_and_expression($tokens, $pos);
        return [['infix', $op, $lhs, $rhs], $pos];
    }
    return [$lhs, $pos];

}

function parse_equality_expression(array $tokens, int $pos): array
{
    [$lhs, $pos] = parse_relational_expression($tokens, $pos);
    $op = $tokens[$pos][0];
    if (in_array($op, ['==', '!=', '===', '!=='])) {
        ++$pos;
        [$rhs, $pos] = parse_equality_expression($tokens, $pos);
        return [['infix', $op, $lhs, $rhs], $pos];
    }
    return [$lhs, $pos];

}

function parse_relational_expression(array $tokens, int $pos): array
{
    [$lhs, $pos] = parse_concatenate_expression($tokens, $pos);
    $op = $tokens[$pos][0];
    if (in_array($op, ['<', '<=', '>', '>='])) {
        ++$pos;
        [$rhs, $pos] = parse_relational_expression($tokens, $pos);
        return [['infix', $op, $lhs, $rhs], $pos];
    }
    return [$lhs, $pos];

}

function parse_concatenate_expression(array $tokens, int $pos): array
{
    [$lhs, $pos] = parse_additive_expression($tokens, $pos);
    $op = $tokens[$pos][0];
    if ($op === '.') {
        ++$pos;
        [$rhs, $pos] = parse_concatenate_expression($tokens, $pos);
        return [['infix', $op, $lhs, $rhs], $pos];
    }
    return [$lhs, $pos];

}

function parse_additive_expression(array $tokens, int $pos): array
{
    [$lhs, $pos] = parse_multiplicative_expression($tokens, $pos);
    $op = $tokens[$pos][0];
    if (in_array($op, ['+', '-'])) {
        ++$pos;
        [$rhs, $pos] = parse_additive_expression($tokens, $pos);
        return [['infix', $op, $lhs, $rhs], $pos];
    }
    return [$lhs, $pos];

}

function parse_multiplicative_expression(array $tokens, int $pos): array
{
    [$lhs, $pos] = parse_prefix_expression($tokens, $pos);
    $op = $tokens[$pos][0];
    if (in_array($op, ['*', '/', '%'])) {
        ++$pos;
        [$rhs, $pos] = parse_multiplicative_expression($tokens, $pos);
        return [['infix', $op, $lhs, $rhs], $pos];
    }
    return [$lhs, $pos];

}

function parse_prefix_expression(array $tokens, int $pos): array
{
    $op = $tokens[$pos][0];
    if (in_array($op, ['!', '+', '-', '++', '--'])) {
        $pos++;
        [$operand, $pos] = parse_prefix_expression($tokens, $pos);
        return [['prefix', $op, $operand], $pos];
    }
    return parse_postfix_expression($tokens, $pos);
}

function parse_postfix_expression(array $tokens, int $pos): array
{
    [$operand, $pos] = parse_primary_expression($tokens, $pos);
    while (true) {
        $op = $tokens[$pos][0];
        if (in_array($op, ['++', '--'])) {
            $pos++;
            $operand = ['postfix', $op, $operand];
        } elseif ($op === '(') {
            $pos++;
            [$args, $pos] = parse_arguments($tokens, $pos);
            $pos++;
            $operand = ['call', $operand, $args];
        } elseif ($op === '[') {
            $pos++;
            if ($tokens[$pos][0] === ']') {
                $index = null;
            } else {
                [$index, $pos] = parse_expression($tokens, $pos);
            }
            expect_token($tokens, $pos, ']');
            $pos++;
            $operand = ['index', $operand, $index];
        } else {
            break;
        }
    }
    return [$operand, $pos];
}

function parse_primary_expression(array $tokens, int $pos): array
{
    $t = $tokens[$pos][0];
    if ($t === 'variable') {
        $name = $tokens[$pos][1];
        $pos++;
        return [['variable', $name], $pos];
    } elseif ($t === '[') {
        $pos++; // [
        [$elements, $pos] = parse_array_elements($tokens, $pos);
        $pos++; // ]
        return [['array', $elements], $pos];
    } elseif ($t === 'constant_encapsed_string') {
        $value = $tokens[$pos][1];
        $pos++;
        return [['string_literal', $value], $pos];
    } elseif ($t === 'lnumber') {
        $value = $tokens[$pos][1];
        $pos++;
        return [['number_literal', intval($value)], $pos];
    } elseif ($t === 'string') {
        $name = $tokens[$pos][1];
        $pos++;
        return [['string', $name], $pos];
    } elseif ($t === '(') {
        $pos++; // (
        [$expr, $pos] = parse_expression($tokens, $pos);
        $pos++; // )
        return [$expr, $pos];
    }
    throw new \RuntimeException("invalid expr: {$t} at {$pos}");
}

function parse_array_elements(array $tokens, int $pos): array
{
    $elements = [];
    while (true) {
        if ($tokens[$pos][0] === ']') {
            break;
        }
        if ($tokens[$pos][0] === ',') {
            ++$pos;
            if ($tokens[$pos][0] === ']') {
                break;
            }
        }
        [$element, $pos] = parse_array_element($tokens, $pos);
        $elements[] = $element;
    }
    return [$elements, $pos];
}

function parse_array_element(array $tokens, int $pos): array
{
    $key = null;
    [$value, $pos] = parse_expression($tokens, $pos);
    return [['element', $key, $value], $pos];
}

function parse_arguments(array $tokens, int $pos): array
{
    $args = [];
    while (true) {
        if ($tokens[$pos][0] === ')') {
            break;
        }
        if ($tokens[$pos][0] === ',') {
            ++$pos;
        }
        [$arg, $pos] = parse_expression($tokens, $pos);
        $args[] = $arg;
    }
    return [$args, $pos];
}

function expect_token(array $tokens, int $pos, string $token): void
{
    if ($tokens[$pos][0] !== $token) {
        throw new \RuntimeException("expect {$token} but got {$tokens[$pos][0]} at {$pos}");
    }
}

function evaluate(array $env, array $node): array
{
    if ($node[0] === 'statements') {
        return evaluate_statements($env, $node[1]);
    } elseif ($node[0] === 'while') {
        return evaluate_while_statement($env, $node[1], $node[2]);
    } elseif ($node[0] === 'for') {
        return evaluate_for_statement($env, $node[1], $node[2], $node[3], $node[4]);
    } elseif ($node[0] === 'if') {
        return evaluate_if_statement($env, $node[1], $node[2], $node[3]);
    } elseif ($node[0] === 'continue') {
        return evaluate_continue_statement($env);
    } elseif ($node[0] === 'break') {
        return evaluate_break_statement($env);
    } elseif ($node[0] === 'return') {
        return evaluate_return_statement($env, $node[1]);
    } elseif ($node[0] === 'echo') {
        return evaluate_echo_statement($env, $node[1]);
    } elseif ($node[0] === 'expression') {
        return evaluate_expression_statement($env, $node[1]);
    } elseif ($node[0] === 'const') {
        return evaluate_const_declaration($env, $node[1], $node[2]);
    } elseif ($node[0] === 'function') {
        return evaluate_function_declaration($env, $node[1], $node[2], $node[3]);
    } elseif ($node[0] === 'assign') {
        return evaluate_assign_expr($env, $node[1], $node[2], $node[3]);
    } elseif ($node[0] === 'infix') {
        return evaluate_infix_expr($env, $node[1], $node[2], $node[3]);
    } elseif ($node[0] === 'prefix') {
        return evaluate_prefix_expr($env, $node[1], $node[2]);
    } elseif ($node[0] === 'postfix') {
        return evaluate_postfix_expr($env, $node[1], $node[2]);
    } elseif ($node[0] === 'call') {
        return evaluate_call_expr($env, $node[1], $node[2]);
    } elseif ($node[0] === 'array') {
        return evaluate_array_expr($env, $node[1]);
    } elseif ($node[0] === 'index') {
        return evaluate_index_expr($env, $node[1], $node[2]);
    } elseif ($node[0] === 'element') {
        return evaluate_array_element($env, $node[1], $node[2]);
    } elseif ($node[0] === 'variable') {
        return evaluate_variable_expr($env, $node[1]);
    } elseif ($node[0] === 'string') {
        return evaluate_string_expr($env, $node[1]);
    } elseif ($node[0] === 'string_literal') {
        return evaluate_literal_expr($env, $node[1]);
    } elseif ($node[0] === 'number_literal') {
        return evaluate_literal_expr($env, $node[1]);
    }
    throw new \RuntimeException("unknown node: {$node[0]}");
}

function evaluate_if_statement(
    array $env,
    array $condition,
    mixed $then,
    mixed $else,
): array {
    [$env, $condition_] = evaluate($env, $condition);
    if ($condition_) {
        if ($then !== null) {
            return evaluate($env, $then);
        }
    }
    if ($else !== null) {
        return evaluate($env, $else);
    }
    return [$env, null];
}

function evaluate_break_statement(array $env): array
{
    return [$env, ['break']];
}

function evaluate_continue_statement(array $env): array
{
    return [$env, ['continue']];
}

function evaluate_return_statement(array $env, mixed $ret): array
{
    if ($ret === null) {
        return [$env, ['return', null]];
    }
    [$env, $ret_] = evaluate($env, $ret);
    return [$env, ['return', $ret_]];
}

function evaluate_echo_statement(array $env, array $expr): array
{
    [$env, $expr_] = evaluate($env, $expr);
    echo $expr_;
    return [$env, null];
}

function evaluate_statements(array $env, array $statements): array
{
    for ($i = 0; $i < count($statements); $i++) {
        [$env, $result] = evaluate($env, $statements[$i]);
        if ($result) {
            return [$env, $result];
        }
    }
    return [$env, null];
}

function evaluate_while_statement(array $env, array $cond, array $body): array
{
    while (true) {
        [$env, $cond_] = evaluate($env, $cond);
        if (! $cond_) {
            break;
        }
        [$env, $result] = evaluate($env, $body);
        if ($result) {
            if ($result[0] === 'break') {
                break;
            }
            if ($result[0] === 'continue') {
                continue;
            }
            return [$env, $result];
        }
    }
    return [$env, null];
}

function evaluate_for_statement(
    array $env,
    array $init,
    array $cond,
    array $update,
    array $body,
): array {
    [$env, $init_] = evaluate($env, $init);
    while (true) {
        [$env, $cond_] = evaluate($env, $cond);
        if (! $cond_) {
            break;
        }
        [$env, $result] = evaluate($env, $body);
        if ($result) {
            if ($result[0] === 'break') {
                break;
            }
            if ($result[0] === 'continue') {
                [$env, $update_] = evaluate($env, $update);
                continue;
            }
            return [$env, $result];
        }
        [$env, $update_] = evaluate($env, $update);
    }
    return [$env, null];
}

function evaluate_expression_statement(array $env, array $expr): array
{
    [$env, $_] = evaluate($env, $expr);
    return [$env, null];
}

function evaluate_const_declaration(array $env, string $name, mixed $value): array
{
    [$env, $value_] = evaluate($env, $value);
    $env['consts'][$name] = $value_;
    return [$env, null];
}

function evaluate_function_declaration(
    array $env,
    string $name,
    array $parameters,
    array $body,
): array {
    $env['funcs'][$name] = [$parameters, $body];
    return [$env, null];
}

function evaluate_assign_expr(array $env, string $operator, array $lhs, array $rhs): array
{
    [$env, $rhs_] = evaluate($env, $rhs);
    if ($lhs[0] === 'variable') {
        $name = $lhs[1];
        if ($operator === '=') {
            $env['vars'][$name] = $rhs_;
        } else {
            $lhs_ = $env['vars'][$name];
            if ($operator === '.=') {
                $rhs_ = $lhs_ . $rhs_;
            } elseif ($operator === '+=') {
                $rhs_ = $lhs_ + $rhs_;
            } elseif ($operator === '-=') {
                $rhs_ = $lhs_ - $rhs_;
            } else {
                throw new \RuntimeException("unsupported compound assignment: {$operator}");
            }
            $env['vars'][$name] = $rhs_;
        }
    } elseif ($lhs[0] === 'index') {
        $keys = [];
        while ($lhs[0] === 'index') {
            if ($lhs[2] === null) {
                $key = null;
            } else {
                [$env, $key] = evaluate($env, $lhs[2]);
            }
            $keys[] = $key;
            $lhs = $lhs[1];
        }
        if ($lhs[0] !== 'variable') {
            throw new \RuntimeException('unsupported');
        }
        $root_var_name = $lhs[1];
        $keys = array_reverse($keys);
        $n = count($keys);
        $arrays = [];
        $a = $env['vars'][$root_var_name];
        for ($i = 0; $i < $n; $i++) {
            $arrays[] = $a;
            if ($keys[$i] === null) {
                $keys[$i] = count($a);
                $a = [];
            } elseif ($i !== $n - 1) {
                $a = $a[$keys[$i]];
            }
        }
        $arrays[$n - 1][$keys[$n - 1]] = $rhs_;
        for ($i = $n - 2; $i >= 0; $i--) {
            $arrays[$i][$keys[$i]] = $arrays[$i + 1];
        }
        if ($operator === '=') {
            $env['vars'][$root_var_name] = $arrays[0];
        } else {
            throw new \RuntimeException('unsupported');
        }
    } elseif ($lhs[0] === 'array') {
        for ($i = 0; $i < count($lhs[1]); $i++) {
            $element = $lhs[1][$i];
            if ($element[0] === 'element') {
                $value = $element[2];
                if ($element[2][0] === 'variable') {
                    $name = $element[2][1];
                    $env['vars'][$name] = $rhs_[$i];
                } else {
                    throw new \RuntimeException('unsupported');
                }
            } else {
                throw new \RuntimeException('unsupported');
            }
        }
    } else {
        throw new \RuntimeException("unsupported: {$lhs[0]}");
    }
    return [$env, null];
}

function evaluate_call_expr(array $env, array $func, array $args): array
{
    if ($func[0] === 'string') {
        $name = $func[1];
    } else {
        [$env, $func_] = evaluate($env, $func);
        $name = $func_;
    }
    $args_ = [];
    for ($i = 0; $i < count($args); $i++) {
        [$env, $arg] = evaluate($env, $args[$i]);
        $args_[$i] = $arg;
    }
    if (array_key_exists($name, $env['funcs'])) {
        $fn = $env['funcs'][$name];
        $params = $fn[0];
        $body = $fn[1];
        $local_env = $env;
        $local_env['vars'] = [];
        if (count($params) !== count($args_)) {
            throw new \RuntimeException("wrong number of arguments ({$name}): expect " . count($params) . ' but got ' . count($args_));
        }
        for ($i = 0; $i < count($params); $i++) {
            $local_env['vars'][$params[$i]] = $args_[$i];
        }
        [$local_env, $ret_] = evaluate($local_env, $body);
        if ($ret_ && $ret_[0] === 'return') {
            $ret = $ret_[1];
        } else {
            $ret = null;
        }
    } elseif ($name === 'defined') {
        $ret = array_key_exists($args_[0], $env['consts']) || defined($args_[0]);
    } else {
        $ret = call_user_func_array($name, $args_);
    }
    return [$env, $ret];
}

function evaluate_array_expr(array $env, array $elements): array
{
    $elements_ = [];
    for ($i = 0; $i < count($elements); $i++) {
        [$env, $element] = evaluate($env, $elements[$i]);
        $elements_[$i] = $element;
    }
    return [$env, $elements_];
}

function evaluate_index_expr(array $env, array $seq, array $index): array
{
    [$env, $seq_] = evaluate($env, $seq);
    [$env, $index_] = evaluate($env, $index);
    if (is_string($seq_)) {
        if ($index_ >= 0 && $index_ < strlen($seq_)) {
            return [$env, $seq_[$index_]];
        }
        return [$env, ''];
    } elseif (is_array($seq_)) {
        if (array_key_exists($index_, $seq_)) {
            return [$env, $seq_[$index_]];
        }
        return [$env, null];
    }
    throw new \RuntimeException('unsupported: ' . gettype($seq_));
}

function evaluate_array_element(array $env, $_, array $value): array
{
    return evaluate($env, $value);
}

function evaluate_infix_expr(
    array $env,
    string $operator,
    array $left,
    array $right,
): array {
    if ($operator === '&&' || $operator === '||') {
        return evaluate_short_circuit_expr($env, $operator, $left, $right);
    }
    [$env, $left_] = evaluate($env, $left);
    [$env, $right_] = evaluate($env, $right);
    if ($operator === '%') {
        return [$env, $left_ % $right_];
    } elseif ($operator === '+') {
        return [$env, $left_ + $right_];
    } elseif ($operator === '-') {
        return [$env, $left_ - $right_];
    } elseif ($operator === '*') {
        return [$env, $left_ * $right_];
    } elseif ($operator === '/') {
        return [$env, $left_ / $right_];
    } elseif ($operator === '.') {
        return [$env, $left_ . $right_];
    } elseif ($operator === '<') {
        return [$env, $left_ < $right_];
    } elseif ($operator === '>') {
        return [$env, $left_ > $right_];
    } elseif ($operator === '<=') {
        return [$env, $left_ <= $right_];
    } elseif ($operator === '>=') {
        return [$env, $left_ >= $right_];
    } elseif ($operator === '==') {
        return [$env, $left_ == $right_];
    } elseif ($operator === '!=') {
        return [$env, $left_ != $right_];
    } elseif ($operator === '===') {
        return [$env, $left_ === $right_];
    } elseif ($operator === '!==') {
        return [$env, $left_ !== $right_];
    }
    throw new \RuntimeException("unsupported operator: {$operator}");
}

function evaluate_short_circuit_expr(
    array $env,
    string $operator,
    array $left,
    array $right,
): array {
    [$env, $left_] = evaluate($env, $left);
    if ($operator === '&&') {
        if (! $left_) {
            return [$env, false];
        }
    } elseif ($operator === '||') {
        if ($left_) {
            return [$env, true];
        }
    }
    [$env, $right_] = evaluate($env, $right);
    return [$env, $right_];
}

function evaluate_prefix_expr(array $env, string $operator, array $operand): array
{
    [$env, $operand_] = evaluate($env, $operand);
    if ($operator === '!') {
        return [$env, ! $operand_];
    } elseif ($operator === '+') {
        return [$env, +$operand_];
    } elseif ($operator === '-') {
        return [$env, -$operand_];
    } elseif ($operator === '++') {
        if ($operand[0] === 'variable') {
            $name = $operand[1];
            $result = $operand_ + 1;
            $env['vars'][$name] = $result;
            return [$env, $result];
        }
        throw new \RuntimeException('unsupported');
    } elseif ($operator === '--') {
        if ($operand[0] === 'variable') {
            $name = $operand[1];
            $result = $operand_ - 1;
            $env['vars'][$name] = $result;
            return [$env, $result];
        }
        throw new \RuntimeException('unsupported');
    } else {
        throw new \RuntimeException("unsupported operator: {$operator}");
    }
}

function evaluate_postfix_expr(array $env, string $operator, array $operand): array
{
    [$env, $operand_] = evaluate($env, $operand);
    if ($operand[0] === 'variable') {
        $name = $operand[1];
        if ($operator === '++') {
            $result = $operand_ + 1;
        } elseif ($operator === '--') {
            $result = $operand_ - 1;
        } else {
            throw new \RuntimeException("unsupported operator: {$operator}");
        }
        $env['vars'][$name] = $result;
        return [$env, $operand_];
    }
    throw new \RuntimeException('unsupported');
}

function evaluate_variable_expr(array $env, string $name): array
{
    return [$env, $env['vars'][$name]];
}

function evaluate_string_expr(array $env, string $name): array
{
    if (array_key_exists($name, $env['consts'])) {
        return [$env, $env['consts'][$name]];
    }
    return [$env, constant($name)];
}

function evaluate_literal_expr(array $env, mixed $value): array
{
    return [$env, $value];
}

$env = [];
$env['vars'] = [];
$env['funcs'] = [];
$env['consts'] = [];

if (defined('PHPHP')) {
    if (PHPHP < 2) {
        echo "Running" . str_repeat(" on PHPHP", PHPHP) . " on PHP\n";
        $file = './index.php';
    } else {
        $file = './hello.php';
    }
    $env['consts']['PHPHP'] = PHPHP + 1;
} else {
    echo "Running on PHP\n";
    $file = './index.php';
    $env['consts']['PHPHP'] = 1;
}

$tokens = tokenize(file_get_contents($file));
$ast = parse($tokens);
evaluate($env, $ast);
