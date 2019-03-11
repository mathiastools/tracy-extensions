<?php
namespace Mathiastools\TracyExtensions;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Tracy\Debugger;
use Tracy\IBarPanel;

/**
 * @author  Matej Erdős
 * @author  MacFJA
 * @license MIT
 * @see     https://github.com/MacFJA/tracy-doctrine-sql
 */
class DoctrineSql implements IBarPanel
{
    /** @var Configuration The doctrine configuration */
    private $doctrineConfiguration;
    
    /** @var string The name of the panel (Useful if you watch multiple Doctrine instance) */
    private $name;
    
    /**
     * Initialize the panel (set a SQL logger)
     *
     * @param Configuration $doctrineConfiguration The doctrine configuration
     * @param string        $name                  The name of the panel (Useful if you watch multiple Doctrine instance)
     */
    public function __construct(Configuration $doctrineConfiguration, $name = '')
    {
        $doctrineConfiguration->setSQLLogger(new DebugStack());
        $this->doctrineConfiguration = $doctrineConfiguration;
        $this->name = $name;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTab()
    {
        $data = $this->doctrineConfiguration->getSQLLogger()->queries;
        
        $totalTime = 0.0;
        foreach ($data as $query) {
            $totalTime += $query['executionMS'];
        }
        $totalTimeMiliseconds = round($totalTime * 1000, 2);
        
        $template = '';
        $name = ($this->name !== '') ? $this->name . ' ' . $totalTimeMiliseconds . ' ms' : 'Queries';
        $template .= '
        <img
            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAyCAYAAADbTRIgAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAadEVYdFNvZnR3YXJlAFBhaW50Lk5FVCB2My41LjEwMPRyoQAABONJREFUWEe9WFtsVEUYrtEQE4zRqA8mBqJGHlTWiBdAUXxAH9QEL2gQFJSrGoFtK4Ix3q8J1kIVCREEEjCaqNEY0QcevEIDRKi0SQm2AhZdlaqlu3vO7O454/ef/q175sycy7qHL/nS7cz8//ftnNl/5kxDFIpLM2dZSzLPgj34XMbff8AP8flyHnJyAeELYOAwKDW0wJk89OQBopurTOhYAe/m4enDahxPpv6oMmCiDU7jsHSRz14xCmJulXgYB4tLMhM5ND0Uh2bqT0U8jP0wlv7ih9B6RTiKx2DsQg5PB/j1nQ+hnCIcxUMUxynSAb75VRAaUISj2AFjZ3OKdABjN0GI6pLOgInfw9hoTpEOIDIdpIquM2DilzA2ilOkA4jMBeOWiWF+UFyWOZVTpAOIZBXROFyfz44/hVPUH/mmy8jYi4poHL6KgsxZ6ogCCirWyGQIbFcE43I5p6oPYOYSJP1UEUlKWo8LOGXtyC/LnIaS8BSSJS0JJtLJYganTw7Mzlgk2FWVsF6kk8UtLBMfMDQVgeEbcuPVUqy6T9ovTdf3h7MATmG5aOBx3YUA+ja6ZB5LG5uke6JfDsM5fEDar9ypHRtCOlmMY1kzMEN3YHBJCfbRfv42KSsltlOF4gkpXp+tjQlhFzTPYPkg0DkJg4pKUIClbc+wCw2swVqMvcMW/IChc9DZpwzWUrTOYQcGFGGs5QFtrIFUKoJHajRuqRoUSad7FzswwMpL8UYiY52+PRKzdCUaE2209pNTpdt3kB0YQMYwq7p4A2exJW+WtiqdsWivvEE6R7vYgQE2jK1+UBuvYTttZQ2FpZkz8U/k4jbRfuJ66fz8IzswwC7ENoanNo5q0gxdZyIuv046PT+wAwPiG2uiR9emNI7QfvpmKdYuluLtR6R4a6EUbfPBeV5y0TrXW8iiZTZKwCwp1syT7vE+dmAA6pj9cmSB/YhMfaU0Sis7QVb2bpfSdTlb/eDmeqXVdI1fz89uMhW4wCh/vIpTpIPyJ60+PYV5MjWoNErnYDuHpwO3r9unp9AlU4E3FKd3P4enAzfX49NTKMiUUBpl+fO1HJ4Oyp+1+fQU5shU8LW8+VpZ2b8jnYXef0xaj0/y6/m5m0x9pzSO0F55o/cTFi33S/HmgqHSsO5RLhGLUB7QtuYhbyuh/92/fmVpA8oCZSRy29lEplYrjclJxbN3HysbUC7J0oZGfXwVUcwXkalb1Y5E9LaZDlY2QBQxuw/r4/10sc2MoRMC3dj9rnTGor1iinSOhG/IbmEgziMb5jdyxUUjp4TnlM5o4qUhar9zB45L+7V79PEa4tHdO+QIwGzRXXmcS9cRlndsZmk96Fdmv3C7NtbAAwX1IgSNc5RBRtIBT/viwHB/6/E2c12sgbSWgsfhQta7eH1fGawlvRiY4Bzp9EqJLi6EG9hGEHA7GgP2KgFBori6f+fYxn9wDu1BeZisjzGzAwfN8Bs/GDsPAzuVwABpAdOseEANqnz9XtSRRMej0BvD0uHAwHMREOsOgcqC1TxR2xfBn6BzMUvGA6b0dASuA5NeJ8bhF/TFWSo5UDumIUmXkrRW5pBv/mA28/+vGql+INlMJP0WrGXm9oGPRS7oWkELEwILwXfBdpBe9elCjS7D6O8v4E5wI7iY1o3dfClHx0FDw7+Sb2560wLhYgAAAABJRU5ErkJggg=="
            style="height: 15px"
        /><span class="tracy-label" title="' . $this->name . '">' . $name . ' (' . count($data) . ')</span>';
        
        return $template;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPanel()
    {
        $data = $this->doctrineConfiguration->getSQLLogger()->queries;
        
        $template = '<h1>SQL Queries';
        if (!empty($this->name)) {
            $template .= ' &mdash; ' . $this->name;
        }
        $template .= '</h1>
            <div class="tracy-inner tracy-DoctrineSql">
                <table>
                    <tr>
                        <th>SQL</th>
                        <th>Params</th>
                        <th>Types</th>
                        <th>Time (ms)</th>
                    </tr>';
        
        foreach ($data as $item) {
            $itemMiliseconds = $item['executionMS'] * 1000;
            $template .= "<tr>
                            <td>{$item['sql']}</td>
                            <td><pre>{$this->formatArrayData($item['params'])}</pre></td>
                            <td><pre>{$this->transformNumericType($this->formatArrayData($item['types']))}</pre></td>
                            <td>{$itemMiliseconds}</td>
                        </tr>";
        }
        $template .= "
                </table>
            </div>";
        
        return $template;
    }
    
    /**
     * Create and initialize a new Doctrine Sql tab/panel.
     * The panel will be attach to the Tracy Debugger Bar.
     *
     * @param EntityManagerInterface $entityManager The doctrine manager to watch
     * @param string                 $name
     */
    public static function init(EntityManagerInterface $entityManager, $name = '')
    {
        Debugger::getBar()->addPanel(new static($entityManager->getConnection()->getConfiguration(), $name));
    }
    
    protected function formatArrayData($data)
    {
        return preg_replace(
            '#^\s{4}#m', '', // Remove 1rst "tab" of the JSON result
            substr(
                json_encode($data, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK),
                2, // Remove "[\n"
                -2 // Remove "\n]"
            )
        );
    }
    
    /**
     * @param mixed $data
     *
     * @return string|string[]|null
     */
    protected function transformNumericType($data)
    {
        $search = [
            '#\b101\b#', // Array of int
            '#\b102\b#', // Array of string
        
        ];
        $replace = [
            'integer[]', // Array of int
            'string[]', // Array of string
        ];
        
        return preg_replace($search, $replace, $data);
    }
}