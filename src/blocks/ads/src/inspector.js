import { InspectorControls } from "@wordpress/block-editor";
import { PanelBody, SelectControl, ToggleControl } from "@wordpress/components";

const options = {
  layout: [
    { label: "Header (900x250 - Horizontal)", value: "header" },
    { label: "Billboard (900x250 - Horizontal)", value: "billboard" },
    { label: "Arroba (300x250 - Quadrado)", value: "arroba" },
    { label: "Meia Página (300x600 - Vertical)", value: "halfpage" },
  ],
};

export default function Inspector({ attributes, setAttributes, debugState }) {
  const [debug, setDebug] = debugState;
  return (
    <InspectorControls>
      <PanelBody title="Definições" icon="money" initialOpen={true}>
        <SelectControl
          label="Formato da Publicidade"
          options={options.layout}
          value={attributes.layout}
          onChange={(val) => setAttributes({ layout: val })}
        />
      </PanelBody>
      <PanelBody title="Desenvolvimento" initialOpen={false}>
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
    </InspectorControls>
  );
}
