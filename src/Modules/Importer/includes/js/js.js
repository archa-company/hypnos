const importer = {
  page: 1,
  totalPages: 0,
  data: [],
  noBreak: false,
  posts: [],
  length: 50,
  s3: {
    url: ''
  },
  elastic: {
    url: '',
    index: '',
    user: null,
    pass: null
  },
  auth: {
    url: '',
    nounce: ''
  },
  loading: null,
  isLoading: false,
  home: '',
  import: async index => {
    importer.setLoading(index, true);
    try {
      const l = c => `${c[c.length - 1] === '/' ? c.substring(0, c.length - 1) : c}.json`,
        d = await (await fetch(`${importer.s3.url}/${importer.data[index].site}/json/routes${l(importer.data[index].uri)}`))
          .json(),
        v = c => ({
          name: c.name,
          description: c.description,
          parent: !!c.parent ? v(c.parent) : {}
        }),
        k = await (await fetch(`${importer.home}/wp-json/morpheus/import`, {
          method: 'POST',
          body: JSON.stringify({
            id: importer.data[index].id,
            auth: importer.auth,
            slug: importer.data[index].slug,
            status: importer.data[index].status,
            title: d.title,
            thumbnail: d.thumbnail,
            author: importer.auth.author,
            content: d.contentHtml,
            categories: !!d.taxonomies.category ?
              d.taxonomies.category.map(x => v(x)) :
              [],
            tags: !!d.taxonomies.post_tag ?
              d.taxonomies.post_tag.map(x => x.name) :
              [],
            credits: !!d.taxonomies.credit ?
              d.taxonomies.credit.map(x => x.name) :
              [],
            seotitle: d.seo.title,
            seodescription: d.seo.description
          }),
          headers: {
            'X-WP-Nonce': importer.auth.nounce,
            Accept: 'application/json',
            'Content-Type': 'application/json'
          }
        })).json();
      importer.setLoading(index, false);
      if (k.stateId > 0) importer.publishment(k.url);
    } catch (e) { console.log(e); importer.setLoading(index, false, true) }
  },
  guest: i => importer.modal.show('Importar', `<div style="display:flex;flex-direction:column;justify-content:center;align-items:center"><div>deseja importar essa matéria?</div><div><input type="button" value="importar" class="IPMinput" onclick="if(!importer.isLoading)importer.import(${i})"></div></div>`),
  publishment: s => importer.modal.show('Matéria', `<div style="display:flex;flex-direction:column;justify-content:center;align-items:center"><a class="IPMa IPMcenter IPMinput" href="${s}">Abrir matéria</a></div>`),
  search: async (page, text, category, region) => {
    importer.page = +page;
    importer.length = +importer.length;
    importer.totalPages = 0;
    importer.setLoadingS(true);
    try {
      const q = {
        from: (importer.length * importer.page) - importer.length,
        size: importer.length,
        sort: [{ updatedAt: { order: 'desc' } }],
        query: {
          bool: {
            filter: [
              {
                term: {
                  type: "post"
                }
              }
            ]
          }
        }
      }, headers = {
        Accept: 'application/json',
        'Content-Type': 'application/json'
      };
      if (!!importer.elastic.user && !!importer.elastic.pass) headers.Authorization = `Basic ${btoa(`${importer.elastic.user}:${importer.elastic.pass}`)}`;
      if (!!region || !!category || !!text) {
        if (!!region) q.query.bool.filter.push({
          term: {
            "taxonomies.post_tag.slug.keyword": region
          }
        });
        if (!!category) q.query.bool.filter.push({
          term: {
            "taxonomies.category.slug.keyword": category
          }
        });
        if (!!text) q.query.bool.must = {
          bool: {
            should: [
              { match_phrase: { title: text } },
              { match_phrase: { slug: text } }
            ]
          }
        };
      };
      const r = await (await fetch(`${importer.elastic.url}/${importer.elastic.index}/_search`, {
        method: 'POST',
        body: JSON.stringify(q),
        headers
      })).json(),
        v = x => !!x && x.length ? x[0].name : '';
      importer.totalPages = Math.ceil(+r.hits.total.value / +importer.length);
      importer.data = r.hits.hits
        .map(x => ({
          id: x._source.externalId,
          status: x._source.status,
          slug: x._source.slug,
          uri: x._source.uri,
          site: x._source.site,
          data: [
            x._source.createdAt,
            x._source.title,
            v(x._source.taxonomies?.category),
            v(x._source.taxonomies?.credit)
          ],
          import: !importer.posts.includes(x._source.slug)
        }));
      importer.setData()
    } catch (e) { console.log(e) }
    importer.setLoadingS(false)
  },
  previous: async (text, category, region) => await importer.search(
    importer.page <= 1 ?
      importer.totalPages :
      importer.page - 1,
    text, category, region
  ),
  next: async (text, category, region) => await importer.search(
    importer.page >= importer.totalPages ?
      1 :
      importer.page + 1,
    text, category, region
  ),
  modal: {
    object: () => document.getElementsByClassName('modal')[0],
    e: () => document.getElementById('modal'),
    title: () => importer.modal.e().getElementsByTagName('div')[1],
    text: () => importer.modal.e().getElementsByTagName('div')[3],
    show: (title, message) => {
      importer.modal.object().style.display = 'flex';
      importer.modal.title().innerHTML = title;
      importer.modal.text().innerHTML = message;
    },
    close: () => {
      if (!event.target.closest('#modal') || event.target.closest('.close'))
        importer.modal.object().style.display = 'none';
    }
  },
  setData: () => {
    const t = document
      .getElementsByClassName('datatable')[0]
      .getElementsByTagName('table')[0],
      h = document
        .getElementsByClassName('pagination')[0],
      g = h.getElementsByTagName('select')[0],
      c = t.getElementsByTagName('tr')[0];
    if (importer.data.length) h.style.display = 'flex';
    let a = null, b = null;
    t.innerHTML = '';
    t.appendChild(c);
    for (let i = 0; i < importer.data.length; i++) {
      a = document.createElement('tr');
      a.innerHTML = (`<td>${importer.data[i].data.join('</td><td>')}</td><td><input type="button" class="IPMinput" value=${!!importer.data[i].import ? `"importar" onclick="importer.guest(${i})"` : '"importado" disabled'}></td>`);
      t.appendChild(a)
    }
    g.innerHTML = '';
    for (let i = 0; i < importer.totalPages; i++) {
      b = document.createElement('option');
      b.value = i + 1;
      if (i === importer.page - 1)
        b.selected = true;
      b.innerHTML = i + 1;
      g.appendChild(b);
    }
  },
  setLoadingS: (b) => {
    if (importer.loading)
      importer.loading.style.display = b ? 'flex' : 'none'
  },
  setLoading: (i, b, c) => {
    const t = document
      .getElementsByClassName('datatable')[0]
      .getElementsByTagName('table')[0]
      .getElementsByTagName('tr');
    importer.isLoading = b;
    if (importer.modal.text()) {
      if (importer.modal.text().getElementsByTagName('input').length > 0)
        importer.modal.text().getElementsByTagName('input')[0].value = b ? '...importando' : c ? 'importar' : 'importado';
      if (c)
        if (importer.modal.text().getElementsByTagName('div').length > 1)
          importer.modal.text().getElementsByTagName('div')[1].innerHTML = "Erro ao tentar importar!<br/>Tentar importar novamente?";
    }
    if (i < t.length - 1) {
      t[i + 1].getElementsByTagName('td')[4].getElementsByTagName('input')[0].value = b ? '...' : c ? 'importar' : 'importado';
      t[i + 1].getElementsByTagName('td')[4].getElementsByTagName('input')[0].disabled = !c
    }
  }
};
(async () => {
  importer.noBreak = true;
  importer.posts = [];
  const g = async p => (await (
    await fetch(
      `${importer.home}/wp-json/wp/v2/posts/?status=publish&&per_page=100&page=${p}&orderby=slug`
    ))
    .json())
    .map(x => x.slug);
  let l = [], p = 1;
  do {
    l = await g(p++);
    if (l.length > 0) importer.posts = importer.posts.concat(l)
  } while (l.length > 0);
  importer.setLoadingS(false)
  console.log('concluido')
})();
// window.addEventListener('load', () => { if (importer.noBreak) importer.setLoadingS(true) })