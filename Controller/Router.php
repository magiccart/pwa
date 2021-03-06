<?php

namespace MageSuite\Pwa\Controller;

class Router
{
    const AVAILABLE_FILES = [
        "manifest.json" => "Manifest",
        "service-worker.js" => "ServiceWorker"
    ];

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\App\Router\ActionList
     */
    protected $actionList;

    /**
     * @var \Magento\Framework\App\Route\Config
     */
    protected $routeConfig;

    /**
     * @var \MageSuite\Pwa\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\Router\ActionList $actionList,
        \Magento\Framework\App\Route\ConfigInterface $routeConfig,
        \MageSuite\Pwa\Helper\Configuration $configuration
    ) {
        $this->actionFactory = $actionFactory;
        $this->actionList = $actionList;
        $this->routeConfig = $routeConfig;
        $this->configuration = $configuration;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->configuration->isEnabled()) {
            return null;
        }

        $identifier = trim($request->getPathInfo(), '/');

        if (!in_array($identifier, array_keys(self::AVAILABLE_FILES))) {
            return null;
        }

        $modules = $this->routeConfig->getModulesByFrontName('magesuite-pwa');

        if (empty($modules)) {
            return null;
        }

        $actionNamespace = $this->getActionNameSpace($identifier);
        $actionClassName = $this->actionList->get($modules[0], null, $actionNamespace, 'index');

        return $this->actionFactory->create($actionClassName);
    }

    protected function getActionNameSpace($identifier)
    {
        return self::AVAILABLE_FILES[$identifier];
    }
}
