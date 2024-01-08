import { Button, Icon, Flex, FlexItem } from "@wordpress/components";

export default function SearchItem({
  id,
  hat,
  site,
  title,
  link,
  image,
  type,
  onSelectPost,
}) {
  return (
    <FlexItem isBlock style={{ flex: '1 1 auto', border: "1px solid #ddd", padding: "1rem" }}>
      <Flex direction="row" align="flex-start" expanded gap={4} wrap={false} >
        {image && (
          <FlexItem style={{ maxWidth: "100px" }}>
            <img
              src={image}
              style={{
                width: "100px",
                aspectRatio: "4/3",
                objectFit: "cover",
                objectPosition: "center",
                backgroundColor: "#ddd",
              }}
            />
          </FlexItem>
        )}
        <FlexItem isBlock>
          <h4 title={title} style={{ fontSize: "1.25rem", lineHeight: 1.4, margin: 0 }}>{title}</h4>
          <div>{link}</div>
          <div>Site: {site} | Chapéu: {hat}</div>
          <div>Tipo: {type} | ID: {id}</div>
        </FlexItem>
        <FlexItem>
          <Flex direction="column" expanded gap={2} wrap={true}>
            <Button
              variant="secondary"
              icon={<Icon icon="yes" />}
              onClick={() => onSelectPost({ id, hat, title, link, image })}
            >
              Selecionar
            </Button>
            <Button
              href={link}
              target="_blank"
              isSmall
              variant="tertiary"
              icon={<Icon icon="visibility" />}
              iconSize={16}
            >
              Visualizar
            </Button>
          </Flex>
        </FlexItem>
      </Flex>
    </FlexItem>
  );
}
