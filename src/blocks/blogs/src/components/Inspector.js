import { InspectorControls } from "@wordpress/block-editor";
import {
  BaseControl,
  ColorPalette,
  PanelBody,
  RangeControl,
  TextControl,
} from "@wordpress/components";
import { colors } from "../config";

export default function Inspector({ attributes, setAttributes }) {
  return (
    <InspectorControls>
      <PanelBody title="Configurações do Card">
        <TextControl
          label="Título"
          value={attributes.title}
          onChange={(val) => setAttributes({ title: val })}
        />
        <RangeControl
          label="Quantidade"
          help="Quantidade de card com matérias dos blogs"
          initialPosition={3}
          value={parseInt(attributes.limit)}
          onChange={(val) => setAttributes({ limit: parseInt(val) })}
          min={1}
          max={12}
        />
        <RangeControl
          label="Número de Colunas"
          help="Quantidade de colunas em que os cards serão distribuídos"
          initialPosition={3}
          value={parseInt(attributes.columns)}
          onChange={(val) => setAttributes({ columns: parseInt(val) })}
          min={1}
          max={4}
        />
        <BaseControl label="Cor do Fundo">
          <ColorPalette
            colors={colors}
            value={attributes.backgroundColor}
            clearable={false}
            disableCustomColors={false}
            enableAlpha={true}
            onChange={(val) => setAttributes({ backgroundColor: val })}
          />
        </BaseControl>
        <BaseControl label="Cor do Título">
          <ColorPalette
            colors={colors}
            value={attributes.titleTextColor}
            clearable={false}
            disableCustomColors={true}
            enableAlpha={true}
            onChange={(val) => setAttributes({ titleTextColor: val })}
          />
        </BaseControl>
        <BaseControl label="Cor da Borda do Título">
          <ColorPalette
            colors={colors}
            value={attributes.titleBorderColor}
            clearable={false}
            disableCustomColors={true}
            enableAlpha={true}
            onChange={(val) => setAttributes({ titleBorderColor: val })}
          />
        </BaseControl>
      </PanelBody>
    </InspectorControls>
  );
}
