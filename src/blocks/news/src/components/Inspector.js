import { InspectorControls } from "@wordpress/block-editor";
import PanelDev from "./PanelDev";
import PanelContent from "./PanelContent";
import PanelAppearance from "./PanelAppearance";

export default function Inspector({
  attributes,
  setAttributes,
  openModal,
  isOpenModal,
  debugState,
}) {
  return (
    <InspectorControls>
      <PanelContent
        initialOpen={false}
        {...{
          attributes,
          setAttributes,
          openModal,
          isOpenModal,
        }}
      />
      <PanelAppearance initialOpen={false} {...{ attributes, setAttributes }} />
      <PanelDev initialOpen={false} debugState={debugState} />
    </InspectorControls>
  );
}
