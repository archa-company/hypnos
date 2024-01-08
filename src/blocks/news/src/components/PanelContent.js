import { MediaUpload, MediaUploadCheck } from "@wordpress/block-editor";
import {
  PanelBody,
  BaseControl,
  TextControl,
  ToggleControl,
  Button,
  Icon,
  Flex,
} from "@wordpress/components";

export default function PanelContent({
  initialOpen,
  attributes,
  setAttributes,
  openModal,
  isOpenModal,
}) {
  return (
    <PanelBody title="Conteúdo" icon="database-view" initialOpen={initialOpen}>
      <BaseControl
        label="Matéria"
        help="Pesquise e selecione uma matéria diretamente do banco de dados do site."
      >
        <div>
          <Button
            variant="primary"
            onClick={openModal}
            disabled={isOpenModal}
            icon={<Icon icon="search" />}
          >
            Pesquisar Matéria
          </Button>
        </div>
      </BaseControl>
      <ToggleControl
        label="Sobrescrever?"
        help="Sobrescreve dados do post no card"
        checked={attributes.overwrite}
        onChange={() => setAttributes({ overwrite: !attributes.overwrite })}
      />
      {attributes.overwrite && (
        <>
          <TextControl
            label="Chapéu"
            value={attributes.hat}
            onChange={(val) => setAttributes({ hat: val })}
          />
          <TextControl
            label="Título"
            value={attributes.title}
            onChange={(val) => setAttributes({ title: val })}
          />
          <TextControl
            label="Link"
            value={attributes.link}
            onChange={(val) => setAttributes({ link: val })}
          />
          <BaseControl label="Imagem">
            {attributes.image && (
              <figure>
                <img
                  src={attributes.image}
                  style={{
                    width: "100%",
                    aspectRatio: "16/9",
                    objectFit: "cover",
                    objectPosition: `${attributes.imagePosX * 100}% ${
                      attributes.imagePosY * 100
                    }%`,
                  }}
                />
              </figure>
            )}
            <Flex direction="row" gap={1}>
              <MediaUploadCheck>
                <MediaUpload
                  title={
                    attributes.image ? "Alterar imagem" : "Selecionar imagem"
                  }
                  value={undefined}
                  onSelect={(media) => {
                    console.log("Media selecionada", media);
                    setAttributes({
                      image: media.sizes?.large?.url || media.sizes.full.url,
                    });
                  }}
                  allowedTypes={["image"]}
                  render={({ open }) => (
                    <Button
                      variant="secondary"
                      style={{
                        flex: 1,
                        justifyContent: "center",
                      }}
                      onClick={open}
                    >
                      {attributes.image ? "Alterar" : "Selecionar Imagem"}
                    </Button>
                  )}
                />
              </MediaUploadCheck>
              {attributes.image && (
                <Button
                  variant="secondary"
                  style={{
                    flex: 1,
                    justifyContent: "center",
                  }}
                  onClick={() =>
                    setAttributes({
                      image: "",
                      imagePosX: undefined,
                      imagePosY: undefined,
                    })
                  }
                >
                  Remover
                </Button>
              )}
            </Flex>
          </BaseControl>
        </>
      )}
    </PanelBody>
  );
}
