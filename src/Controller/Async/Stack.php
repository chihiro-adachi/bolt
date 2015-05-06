<?php
namespace Bolt\Controller\Async;

use Bolt\Response\BoltResponse;
use Silex;
use Symfony\Component\HttpFoundation\Request;

/**
 * Async controller for general async routes.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 * @author Carson Full <carsonfull@gmail.com>
 */
class Stack extends AsyncBase
{
    protected function addRoutes(Silex\ControllerCollection $ctr)
    {
        $ctr->get('/addstack/{filename}', 'actionAddStack')
            ->assert('filename', '.*')
            ->bind('addstack');

        $ctr->get('/showstack', 'actionShowStack')
            ->bind('showstack');
    }

    /**
     * Add a file to the user's stack.
     *
     * @param string $filename
     *
     * @return true
     */
    public function actionAddStack($filename)
    {
        $this->app['stack']->add($filename);

        return true;
    }

    /**
     * Render a user's current stack.
     *
     * @param Request $request
     *
     * @return BoltResponse
     */
    public function actionShowStack(Request $request)
    {
        $count = $request->query->get('items', 10);
        $options = $request->query->get('options', false);

        $context = array(
            'stack'     => $this->app['stack']->listitems($count),
            'filetypes' => $this->app['stack']->getFileTypes(),
            'namespace' => $this->app['upload.namespace'],
            'canUpload' => $this->getUsers()->isAllowed('files:uploads')
        );

        switch ($options) {
            case 'minimal':
                $twig = 'components/stack-minimal.twig';
                break;

            case 'list':
                $twig = 'components/stack-list.twig';
                break;

            case 'full':
            default:
                $twig = 'components/panel-stack.twig';
                break;
        }

        return $this->render($twig, array('context' => $context));
    }
}
