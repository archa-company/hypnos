import { useState } from "@wordpress/element";
import { BaseControl, Button, FocalPointPicker } from "@wordpress/components";

export default function ImagePosition({
  attributes,
  setAttributes,
  setOpenFocal,
}) {
  const [focalPoint, setFocalPoint] = useState({
    x: attributes.imagePosX || 0.5,
    y: attributes.imagePosY || 0.5,
  });
  return (
    <BaseControl
      label="Posicionamento da Image"
      help="Ajuste o posicionamento da imagem para otimizar o foco na área de interesse desejada."
      className="nmb"
      __nextHasNoMarginBottom
    >
      <FocalPointPicker
        url={attributes.image}
        dimensions={{ width: 248, height: 148 }}
        value={focalPoint}
        onChange={(focalPoint) => {
          setFocalPoint(focalPoint);
          setAttributes({
            imagePosX: focalPoint.x,
            imagePosY: focalPoint.y,
          });
        }}
      />
      <Button
        variant="secondary"
        onClick={() => {
          setAttributes({
            imagePosX: focalPoint.x,
            imagePosY: focalPoint.y,
          });
          setOpenFocal(false);
        }}
        style={{ width: "100%", justifyContent: "center" }}
      >
        Confirmar Posicionamento
      </Button>
    </BaseControl>
  );
}
