import {
  PanelBody,
  BaseControl,
  SelectControl,
  ToggleControl,
  ButtonGroup,
  Button,
  Flex,
} from "@wordpress/components";
import { useState } from "@wordpress/element";
import { options } from "../config";
import ImagePosition from "./ImagePosition";

export default function PanelAppearance({
  initialOpen,
  attributes,
  setAttributes,
}) {
  const [openFocal, setOpenFocal] = useState(false);
  return (
    <PanelBody
      title="Aparência"
      icon="admin-appearance"
      initialOpen={initialOpen}
    >
      {!!attributes.image && !!openFocal && (
        <ImagePosition
          {...{
            attributes,
            setAttributes,
            setOpenFocal,
          }}
        />
      )}
      {!openFocal && (
        <>
          <SelectControl
            label="Layout do card"
            options={options.layout}
            value={attributes.layout}
            onChange={(val) => setAttributes({ layout: val })}
          />
          <BaseControl label="Tamanho do card">
            <Flex direction="row" gap={1}>
              <ButtonGroup style={{ width: "100%" }}>
                {options.size.map((option) => (
                  <Button
                    variant={
                      attributes.size === option.value ? "primary" : "secondary"
                    }
                    onClick={() => setAttributes({ size: option.value })}
                  >
                    {option.label}
                  </Button>
                ))}
              </ButtonGroup>
            </Flex>
          </BaseControl>
          <BaseControl label="Imagem">
            <Button
              variant="secondary"
              disabled={openFocal}
              onClick={() => setOpenFocal(!openFocal)}
              style={{
                width: "100%",
                marginBottom: "1rem",
                justifyContent: "center",
              }}
            >
              Alterar Posicionamento
            </Button>
            <ToggleControl
              label="Exibir imagem"
              help="Mostra ou oculta a imagem"
              checked={attributes.hasImage}
              onChange={() => setAttributes({ hasImage: !attributes.hasImage })}
            />
            <ToggleControl
              label="Inverter ordem"
              help="Inverte a ordem da imagem e título"
              checked={attributes.inverted}
              onChange={() => setAttributes({ inverted: !attributes.inverted })}
            />
          </BaseControl>
          <SelectControl
            label="Formato do conteúdo"
            options={options.format}
            value={attributes.format}
            onChange={(val) => setAttributes({ format: val })}
          />
          <ToggleControl
            label="Título em cinza"
            help="Adiciona um fundo cinza no título do card"
            checked={attributes.titleBgGray}
            onChange={() =>
              setAttributes({ titleBgGray: !attributes.titleBgGray })
            }
          />
          <ToggleControl
            label="Caixa branca"
            help="Adiciona um gap e uma caixa branca ao redor do card"
            checked={attributes.boxed}
            onChange={() => setAttributes({ boxed: !attributes.boxed })}
          />
          <ToggleControl
            label="Patrocinado"
            help="Marque se este for um conteúdo pago"
            checked={attributes.sponsored}
            onChange={(val) => {
              const save = {
                sponsored: !attributes.sponsored,
                sponsor: !val
                  ? ""
                  : !!attributes.sponsor
                  ? attributes.sponsor
                  : options.sponsorTypes[0].value,
              };
              setAttributes(save);
            }}
          />
          {attributes.sponsored && (
            <BaseControl
              label="Especial"
              help="O Chapéu do card será sobrescrito por este termo"
            >
              <Flex direction="row" gap={1}>
                <ButtonGroup style={{ width: "100%" }}>
                  {options.sponsorTypes.map((option) => (
                    <Button
                      variant={
                        attributes.sponsor === option.value
                          ? "primary"
                          : "secondary"
                      }
                      onClick={() =>
                        setAttributes({
                          sponsor: option.value,
                          hat: option.value,
                        })
                      }
                    >
                      {option.label}
                    </Button>
                  ))}
                </ButtonGroup>
              </Flex>
            </BaseControl>
          )}
        </>
      )}
    </PanelBody>
  );
}
