import { PanelBody, ToggleControl } from "@wordpress/components";

export default function PanelDev({ initialOpen, debugState }) {
  const [debug, setDebug] = debugState;
  return (
    <PanelBody title="Desenvolvimento" initialOpen={initialOpen}>
      <ToggleControl
        label="Debug - Bloco"
        help="Mostra a saída JSON do bloco"
        checked={debug.block}
        onChange={() => setDebug({ ...debug, block: !debug.block })}
      />
      <ToggleControl
        label="Debug - Classes"
        help="Mostra a saída JSON das Classes CSS"
        checked={debug.classes}
        onChange={() => setDebug({ ...debug, classes: !debug.classes })}
      />
    </PanelBody>
  );
}
