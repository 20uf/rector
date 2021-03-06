<?php declare(strict_types=1);

namespace Rector\Rector\Contrib\Nette\Application;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use Rector\Node\NodeFactory;
use Rector\NodeAnalyzer\MethodCallAnalyzer;
use Rector\Rector\AbstractRector;

/**
 * Before::
 * - $myControl->validateControl(?$snippet)
 *
 * After:
 * - $myControl->redrawControl(?$snippet, false);
 */
final class ValidateControlRector extends AbstractRector
{
    /**
     * @var MethodCallAnalyzer
     */
    private $methodCallAnalyzer;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    public function __construct(MethodCallAnalyzer $methodCallAnalyzer, NodeFactory $nodeFactory)
    {
        $this->methodCallAnalyzer = $methodCallAnalyzer;
        $this->nodeFactory = $nodeFactory;
    }

    public function isCandidate(Node $node): bool
    {
        return $this->methodCallAnalyzer->isTypeAndMethod(
            $node,
            'Nette\Application\UI\Control',
            'validateControl'
        );
    }

    /**
     * @param MethodCall $methodCallNode
     */
    public function refactor(Node $methodCallNode): Node
    {
        $methodCallNode->name = new Identifier('redrawControl');

        $methodCallNode->args[0] = $methodCallNode->args[0] ?? new Arg($this->nodeFactory->createNullConstant());
        $methodCallNode->args[1] = new Arg($this->nodeFactory->createFalseConstant());

        return $methodCallNode;
    }
}
