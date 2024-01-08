import { useState, useEffect } from "@wordpress/element";
import { SearchControl, Modal, Flex, Spinner, Button } from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";
import SearchItem from "./SearchItem";

export default function SearchModal({ setAttributes, close }) {
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(false);
  const [results, setResults] = useState([]);

  function onSelectPost(item) {
    setAttributes({ ...item });
    close();
    setSearch("");
  }

  async function fetchSearchAPI(searchTerm, page, size) {
    page = page || 1;
    size = size || 20;
    const from = size * page - size;

    const payload = {
      from,
      size,
      _source: [
        "site",
        "externalId",
        "title",
        "type",
        "status",
        "slug",
        "hat",
        "uri",
        "thumbnail",
        "createdAt",
        "updatedAt",
        "uuid",
      ],
      sort: [
        {
          createdAt: { order: "desc" },
        },
      ],
    }

    const filters = [];

    if (searchTerm) {
      filters.unshift({
        fuzzy: {
          title: {
            value: searchTerm || "",
            fuzziness: "AUTO",
          },
        },
      });

      payload.query = {
        bool: {
          must: filters,
        }
      }
    }

    const response = await apiFetch({
      path: "/morpheus/v1/elasticsearch",
      method: "POST",
      data: payload,
    });
    return response;
  }

  async function fetchSearch() {
    if (search.length !== 0 && search.length < 3) return;
    setLoading(true);
    const results = await fetchSearchAPI(search);
    setResults(results);
    setLoading(false);
  }

  async function onReset(event) {
    event.preventDefault();
    setSearch('');
  }

  async function onSearch(event) {
    event.preventDefault();
    await fetchSearch();
  }

  useEffect(() => {
    fetchSearch();
  }, []);

  return (
    <Modal
      title="Pesquisar Matéria"
      onRequestClose={close}
      shouldCloseOnClickOutside={false}
      isFullScreen={true}
      className="morpheus-news-modal"
    >
      <form
      onSubmit={onSearch}
      style={{ display: "flex", gap: '1rem',  }}
      >
        <div style={{ flex: 1 }}>
          <SearchControl
            label="Pesquisar Matéria"
            help="Digite o título da matéria que deseja encontrar"
            value={search}
            disabled={loading}
            onChange={(val) => setSearch(val)}
            onClose={onReset}
          />
        </div>
        <Button
          type="submit"
          variant="primary"
          disabled={loading}
          onClick={onSearch}
          style={{ height: '48px' }}
        >Buscar</Button>
      </form>
      <Flex
        direction="column"
        expanded
        gap={2}
        wrap={true}
        className="search-results"
      >
        {loading && (
          <div className="search-placeholder">
            <div className="search-placeholder-loading">
              <Spinner style={{ width: 30, height: 30 }} />
              <div>Pesquisando, aguarde...</div>
            </div>
          </div>
        )}
        {!loading && !results.length && (
          <div className="search-placeholder">
            <div className="search-placeholder-loading">
              <div>Nenhum resultado encontrado.</div>
            </div>
          </div>
        )}
        {!loading &&
          !!results.length &&
          results.map((item) => <SearchItem {...{ ...item, onSelectPost }} />)}
      </Flex>
    </Modal>
  );
}
