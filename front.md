Pronto.

Transformei o layout em uma versão completa de ecommerce visual, com:

* landing page comercial
* catálogo de produtos
* filtros por busca e categoria
* carrinho lateral funcional
* controle de quantidade
* cálculo de subtotal, frete e total
* regra de frete grátis
* checkout completo
* formulário de entrega
* seleção de pagamento
* resumo do pedido
* validação básica
* bloco de depoimentos
* seções de conversão e prova social

Também deixei funções separadas para facilitar evolução:

* `filterProducts`
* `getCartCount`
* `getSubtotal`
* `getShipping`
* `validateCheckout`

E incluí testes básicos com `console.assert` para a lógica principal.

Agora esse front já está em um nível bom para virar projeto real. O próximo passo mais correto é eu separar isso em componentes profissionais, por exemplo:

* `Header`
* `Hero`
* `ProductGrid`
* `CartDrawer`
* `CheckoutModal`
* `Footer`

e depois conectar com Laravel/API para pedidos reais.


import React, { useMemo, useState } from 'react'

type Category = {
  name: string
  description: string
  icon: string
}

type Product = {
  id: number
  name: string
  category: string
  price: number
  unit: string
  badge: string
  image: string
  description: string
}

type Benefit = {
  title: string
  description: string
}

type CartItem = {
  product: Product
  quantity: number
}

type CheckoutForm = {
  name: string
  phone: string
  email: string
  address: string
  neighborhood: string
  city: string
  notes: string
  paymentMethod: string
}

const brand = {
  name: 'Família Mogi',
  slogan: 'Produtos frescos direto do produtor para sua casa',
  whatsapp: '(11) 99999-9999',
  email: 'contato@familiamogi.com.br',
  address: 'Mogi das Cruzes - SP',
  logo: '/mnt/data/logo-sem-fundo.png',
}

const categories: Category[] = [
  {
    name: 'Verduras',
    description: 'Folhas frescas, selecionadas e ideais para consumo diário.',
    icon: '🥬',
  },
  {
    name: 'Legumes',
    description: 'Legumes com aparência premium, sabor natural e excelente giro.',
    icon: '🥕',
  },
  {
    name: 'Cogumelos',
    description: 'Linha especial com cogumelos frescos para varejo e gastronomia.',
    icon: '🍄',
  },
  {
    name: 'Cestas',
    description: 'Combos montados para famílias, assinaturas e pedidos recorrentes.',
    icon: '🧺',
  },
]

const products: Product[] = [
  {
    id: 1,
    name: 'Shimeji Fresco',
    category: 'Cogumelos',
    price: 18.9,
    unit: '200g',
    badge: 'Mais vendido',
    image: 'https://images.unsplash.com/photo-1504545102780-26774c1bb073?auto=format&fit=crop&w=900&q=80',
    description: 'Ideal para refogados, risotos e pratos especiais.',
  },
  {
    id: 2,
    name: 'Cogumelo Paris',
    category: 'Cogumelos',
    price: 12.9,
    unit: '250g',
    badge: 'Oferta',
    image: 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=900&q=80',
    description: 'Versátil para molhos, massas e acompanhamentos.',
  },
  {
    id: 3,
    name: 'Alface Crespa',
    category: 'Verduras',
    price: 4.9,
    unit: 'unidade',
    badge: 'Colhido hoje',
    image: 'https://images.unsplash.com/photo-1498579809087-ef1e558fd1da?auto=format&fit=crop&w=900&q=80',
    description: 'Folhas verdes e crocantes para saladas frescas.',
  },
  {
    id: 4,
    name: 'Couve Manteiga',
    category: 'Verduras',
    price: 6.5,
    unit: 'maço',
    badge: 'Orgânico',
    image: 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=900&q=80',
    description: 'Excelente para refogados, sucos e alimentação funcional.',
  },
  {
    id: 5,
    name: 'Tomate Italiano',
    category: 'Legumes',
    price: 8.9,
    unit: 'kg',
    badge: 'Safra da semana',
    image: 'https://images.unsplash.com/photo-1546094096-0df4bcaaa337?auto=format&fit=crop&w=900&q=80',
    description: 'Tomates firmes, saborosos e ótimos para molhos.',
  },
  {
    id: 6,
    name: 'Cenoura Premium',
    category: 'Legumes',
    price: 7.4,
    unit: 'kg',
    badge: 'Seleção especial',
    image: 'https://images.unsplash.com/photo-1447175008436-170170753d52?auto=format&fit=crop&w=900&q=80',
    description: 'Padrão visual uniforme para varejo e uso doméstico.',
  },
  {
    id: 7,
    name: 'Cesta Família',
    category: 'Cestas',
    price: 39.9,
    unit: 'kit',
    badge: 'Mais econômica',
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=900&q=80',
    description: 'Mix de folhas, legumes e itens essenciais para a semana.',
  },
  {
    id: 8,
    name: 'Cesta Premium com Cogumelos',
    category: 'Cestas',
    price: 59.9,
    unit: 'kit',
    badge: 'Destaque',
    image: 'https://images.unsplash.com/photo-1518843875459-f738682238a6?auto=format&fit=crop&w=900&q=80',
    description: 'Seleção diferenciada com verduras, legumes e cogumelos frescos.',
  },
]

const benefits: Benefit[] = [
  {
    title: 'Identidade da marca preservada',
    description: 'Layout construído a partir das cores, símbolos e proposta visual da logo.',
  },
  {
    title: 'Fluxo completo de compra',
    description: 'Landing page, catálogo, carrinho lateral e checkout em uma experiência única.',
  },
  {
    title: 'Estrutura pronta para escalar',
    description: 'Base ideal para integrar API, WhatsApp, gateway de pagamento e painel admin.',
  },
]

const testimonials = [
  {
    name: 'Mariana Costa',
    role: 'Cliente recorrente',
    text: 'Os produtos chegam muito frescos e o processo de compra ficou simples e profissional.',
  },
  {
    name: 'Carlos Henrique',
    role: 'Restaurante local',
    text: 'A apresentação dos cogumelos e legumes transmite qualidade logo no primeiro contato.',
  },
  {
    name: 'Fernanda Alves',
    role: 'Assinatura semanal',
    text: 'A cesta semanal ajudou muito a organizar minhas compras e reduzir idas ao mercado.',
  },
]

const stats = [
  { value: '+300', label: 'Pedidos por mês' },
  { value: '24h', label: 'Janela de entrega local' },
  { value: '100%', label: 'Foco em frescor visual' },
]

const initialForm: CheckoutForm = {
  name: '',
  phone: '',
  email: '',
  address: '',
  neighborhood: '',
  city: 'Mogi das Cruzes',
  notes: '',
  paymentMethod: 'Pix',
}

export function formatPrice(value: number) {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

export function filterProducts(items: Product[], search: string, selectedCategory: string) {
  const term = search.trim().toLowerCase()

  return items.filter((product) => {
    const matchesCategory = !selectedCategory || product.category === selectedCategory
    const matchesSearch =
      !term ||
      product.name.toLowerCase().includes(term) ||
      product.category.toLowerCase().includes(term) ||
      product.description.toLowerCase().includes(term)

    return matchesCategory && matchesSearch
  })
}

export function getCartCount(cart: CartItem[]) {
  return cart.reduce((total, item) => total + item.quantity, 0)
}

export function getSubtotal(cart: CartItem[]) {
  return cart.reduce((total, item) => total + item.product.price * item.quantity, 0)
}

export function getShipping(subtotal: number) {
  if (subtotal === 0) return 0
  return subtotal >= 80 ? 0 : 8.9
}

export function validateCheckout(form: CheckoutForm, cart: CartItem[]) {
  if (cart.length === 0) return 'Seu carrinho está vazio.'
  if (!form.name.trim()) return 'Informe seu nome.'
  if (!form.phone.trim()) return 'Informe seu telefone.'
  if (!form.address.trim()) return 'Informe seu endereço.'
  if (!form.neighborhood.trim()) return 'Informe seu bairro.'
  if (!form.city.trim()) return 'Informe sua cidade.'
  return ''
}

export function runStoreTests() {
  const filtered = filterProducts(products, 'cogumelo', 'Cogumelos')
  const cart: CartItem[] = [
    { product: products[0], quantity: 2 },
    { product: products[2], quantity: 1 },
  ]
  const subtotal = getSubtotal(cart)
  const shipping = getShipping(subtotal)
  const valid = validateCheckout(
    {
      ...initialForm,
      name: 'Enzo',
      phone: '11999999999',
      address: 'Rua Exemplo, 100',
      neighborhood: 'Centro',
      city: 'Mogi das Cruzes',
    },
    cart,
  )
  const invalid = validateCheckout(initialForm, [])

  console.assert(filtered.length >= 1, 'Deve filtrar produtos por busca e categoria')
  console.assert(getCartCount(cart) === 3, 'Deve contar os itens do carrinho corretamente')
  console.assert(Math.abs(subtotal - 42.7) < 0.001, 'Deve calcular subtotal corretamente')
  console.assert(shipping === 8.9, 'Deve calcular frete abaixo do limite de frete grátis')
  console.assert(valid === '', 'Validação deve passar com formulário completo e carrinho preenchido')
  console.assert(invalid === 'Seu carrinho está vazio.', 'Validação deve falhar com carrinho vazio')
}

function SectionTitle({ eyebrow, title, description }: { eyebrow: string; title: string; description?: string }) {
  return (
    <div>
      <p className="text-sm font-bold uppercase tracking-[0.22em] text-[#2f9c44]">{eyebrow}</p>
      <h2 className="mt-2 text-3xl font-black tracking-tight text-[#2f4b1f] sm:text-4xl">{title}</h2>
      {description ? <p className="mt-4 max-w-2xl text-base leading-7 text-[#5b684f]">{description}</p> : null}
    </div>
  )
}

function QuantityControl({ quantity, onDecrease, onIncrease }: { quantity: number; onDecrease: () => void; onIncrease: () => void }) {
  return (
    <div className="inline-flex items-center rounded-full border border-[#d9dfcf] bg-white">
      <button onClick={onDecrease} className="px-3 py-1.5 text-sm font-bold text-[#5b684f] hover:text-[#2f9c44]">−</button>
      <span className="min-w-10 text-center text-sm font-bold text-[#2f4b1f]">{quantity}</span>
      <button onClick={onIncrease} className="px-3 py-1.5 text-sm font-bold text-[#5b684f] hover:text-[#2f9c44]">+</button>
    </div>
  )
}

export default function FamiliaMogiStore() {
  const [search, setSearch] = useState('')
  const [selectedCategory, setSelectedCategory] = useState('')
  const [cart, setCart] = useState<CartItem[]>([])
  const [cartOpen, setCartOpen] = useState(false)
  const [checkoutOpen, setCheckoutOpen] = useState(false)
  const [checkoutForm, setCheckoutForm] = useState<CheckoutForm>(initialForm)
  const [checkoutMessage, setCheckoutMessage] = useState('')
  const [orderPlaced, setOrderPlaced] = useState(false)

  const filteredProducts = useMemo(() => {
    return filterProducts(products, search, selectedCategory)
  }, [search, selectedCategory])

  const subtotal = useMemo(() => getSubtotal(cart), [cart])
  const shipping = useMemo(() => getShipping(subtotal), [subtotal])
  const total = subtotal + shipping
  const cartCount = useMemo(() => getCartCount(cart), [cart])

  function addToCart(product: Product) {
    setCart((current) => {
      const existing = current.find((item) => item.product.id === product.id)
      if (existing) {
        return current.map((item) =>
          item.product.id === product.id ? { ...item, quantity: item.quantity + 1 } : item,
        )
      }
      return [...current, { product, quantity: 1 }]
    })
    setCartOpen(true)
  }

  function updateQuantity(productId: number, delta: number) {
    setCart((current) =>
      current
        .map((item) =>
          item.product.id === productId ? { ...item, quantity: item.quantity + delta } : item,
        )
        .filter((item) => item.quantity > 0),
    )
  }

  function removeItem(productId: number) {
    setCart((current) => current.filter((item) => item.product.id !== productId))
  }

  function openCheckout() {
    setCheckoutMessage('')
    setOrderPlaced(false)
    setCheckoutOpen(true)
    setCartOpen(false)
  }

  function submitCheckout() {
    const error = validateCheckout(checkoutForm, cart)
    if (error) {
      setCheckoutMessage(error)
      setOrderPlaced(false)
      return
    }

    setCheckoutMessage('Pedido enviado com sucesso. Agora você pode integrar este fluxo com API, WhatsApp ou gateway de pagamento.')
    setOrderPlaced(true)
    setCart([])
    setCheckoutForm(initialForm)
  }

  return (
    <div className="min-h-screen bg-[#f7f6ef] text-[#2b2b2b]">
      <header className="sticky top-0 z-40 border-b border-[#d9dfcf] bg-[#f7f6ef]/95 backdrop-blur">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
          <a href="#inicio" className="flex items-center gap-3">
            <div className="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full border border-[#d8c48a] bg-white shadow-sm">
              <img src={brand.logo} alt={brand.name} className="h-full w-full object-contain p-1" />
            </div>
            <div>
              <p className="text-xl font-black tracking-tight text-[#2f9c44]">{brand.name}</p>
              <p className="text-xs text-[#6f775f]">Verduras, legumes e cogumelos frescos</p>
            </div>
          </a>

          <nav className="hidden items-center gap-8 text-sm font-semibold text-[#4f5a43] md:flex">
            <a href="#inicio" className="transition hover:text-[#2f9c44]">Início</a>
            <a href="#categorias" className="transition hover:text-[#2f9c44]">Categorias</a>
            <a href="#produtos" className="transition hover:text-[#2f9c44]">Loja</a>
            <a href="#como-funciona" className="transition hover:text-[#2f9c44]">Como funciona</a>
            <a href="#depoimentos" className="transition hover:text-[#2f9c44]">Depoimentos</a>
            <a href="#contato" className="transition hover:text-[#2f9c44]">Contato</a>
          </nav>

          <div className="flex items-center gap-3">
            <button className="hidden rounded-full border border-[#9f6a1d] px-4 py-2 text-sm font-bold text-[#7c5316] transition hover:bg-[#9f6a1d] hover:text-white md:inline-flex">
              Catálogo
            </button>
            <button
              onClick={() => setCartOpen(true)}
              className="rounded-full bg-[#2f9c44] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#267c37]"
            >
              Carrinho ({cartCount})
            </button>
          </div>
        </div>
      </header>

      <main>
        <section id="inicio" className="relative overflow-hidden">
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(47,156,68,0.16),_transparent_34%),radial-gradient(circle_at_top_right,_rgba(225,179,0,0.18),_transparent_30%),linear-gradient(180deg,#f7f6ef_0%,#eef5e8_100%)]" />
          <div className="relative mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-24">
            <div>
              <span className="inline-flex rounded-full border border-[#d8c48a] bg-white px-4 py-1.5 text-sm font-bold text-[#9f6a1d] shadow-sm">
                Compra online • Entrega local • Marca familiar
              </span>

              <h1 className="mt-6 max-w-3xl text-4xl font-black leading-tight tracking-tight text-[#2f4b1f] sm:text-5xl lg:text-6xl">
                Landing page completa com loja, carrinho e checkout para a {brand.name}.
              </h1>

              <p className="mt-6 max-w-2xl text-lg leading-8 text-[#5a6550]">
                Uma vitrine digital pensada para converter: identidade visual coerente com a logo, catálogo organizado, produtos em destaque e experiência completa de compra para hortifruti e cogumelos.
              </p>

              <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="#produtos" className="rounded-full bg-[#2f9c44] px-7 py-3.5 text-center text-base font-bold text-white shadow-lg shadow-green-200 transition hover:bg-[#267c37]">
                  Comprar agora
                </a>
                <button onClick={() => setCartOpen(true)} className="rounded-full border border-[#9f6a1d] bg-white px-7 py-3.5 text-base font-bold text-[#7c5316] transition hover:bg-[#f8f0da]">
                  Abrir carrinho
                </button>
              </div>

              <div className="mt-10 grid gap-4 sm:grid-cols-3">
                {stats.map((item) => (
                  <div key={item.label} className="rounded-3xl border border-[#d9dfcf] bg-white/90 p-5 shadow-sm">
                    <p className="text-2xl font-black text-[#2f9c44]">{item.value}</p>
                    <p className="mt-1 text-sm text-[#6f775f]">{item.label}</p>
                  </div>
                ))}
              </div>
            </div>

            <div className="relative">
              <div className="rounded-[36px] border border-[#d8c48a] bg-white p-6 shadow-2xl shadow-[#dfe8d7]">
                <div className="rounded-[28px] bg-[linear-gradient(180deg,#f9fbf5_0%,#f1f7eb_100%)] p-8">
                  <div className="mx-auto flex w-full max-w-sm flex-col items-center text-center">
                    <img src={brand.logo} alt={brand.name} className="w-64 object-contain" />
                    <p className="mt-6 text-sm font-semibold uppercase tracking-[0.18em] text-[#9f6a1d]">
                      Identidade aplicada à conversão
                    </p>
                    <p className="mt-3 text-base leading-7 text-[#596550]">
                      O design valoriza o verde, o dourado do sol e os tons naturais da marca para transmitir frescor, origem confiável e apelo comercial.
                    </p>
                    <div className="mt-6 grid w-full gap-3 text-left">
                      <div className="rounded-2xl border border-[#d9dfcf] bg-white p-4">
                        <p className="text-sm font-bold text-[#2f4b1f]">Checkout simplificado</p>
                        <p className="mt-1 text-sm text-[#6f775f]">Fluxo pronto para integração com meios de pagamento.</p>
                      </div>
                      <div className="rounded-2xl border border-[#d9dfcf] bg-white p-4">
                        <p className="text-sm font-bold text-[#2f4b1f]">Carrinho lateral</p>
                        <p className="mt-1 text-sm text-[#6f775f]">Experiência moderna para compras rápidas no desktop e mobile.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div className="absolute -bottom-5 -left-2 rounded-2xl border border-[#d8c48a] bg-white px-4 py-3 shadow-lg">
                <p className="text-sm font-bold text-[#2f4b1f]">Frete grátis acima de {formatPrice(80)}</p>
                <p className="text-sm text-[#6f775f]">Regra visual já aplicada no carrinho e checkout</p>
              </div>
            </div>
          </div>
        </section>

        <section id="categorias" className="mx-auto max-w-7xl px-6 py-16 lg:px-8">
          <SectionTitle
            eyebrow="Categorias"
            title="Seções preparadas para vender mais"
            description="A estrutura da loja separa os principais grupos da operação e facilita tanto compras avulsas quanto pedidos maiores."
          />

          <div className="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            {categories.map((item) => (
              <div key={item.name} className="group rounded-[30px] border border-[#d9dfcf] bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <div className="mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-[#eef7e7] text-3xl ring-1 ring-[#d9dfcf]">
                  {item.icon}
                </div>
                <h3 className="text-2xl font-black text-[#2f4b1f]">{item.name}</h3>
                <p className="mt-3 text-sm leading-7 text-[#5f6b54]">{item.description}</p>
                <a href="#produtos" className="mt-6 inline-block text-sm font-bold text-[#2f9c44] transition group-hover:translate-x-1">
                  Comprar nessa categoria →
                </a>
              </div>
            ))}
          </div>
        </section>

        <section id="produtos" className="border-y border-[#dde5d4] bg-white/70 py-16">
          <div className="mx-auto max-w-7xl px-6 lg:px-8">
            <div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
              <SectionTitle
                eyebrow="Loja"
                title="Catálogo com filtros, preço e ação direta"
                description="Cards maiores, leitura clara de preço e botão de compra para acelerar a conversão."
              />

              <div className="flex w-full flex-col gap-3 sm:max-w-xl sm:flex-row">
                <input
                  value={search}
                  onChange={(event) => setSearch(event.target.value)}
                  type="text"
                  placeholder="Buscar verduras, legumes, cogumelos..."
                  className="w-full rounded-full border border-[#d9dfcf] bg-white px-5 py-3 text-sm text-[#384131] outline-none transition placeholder:text-[#95a08b] focus:border-[#2f9c44]"
                />
                <select
                  value={selectedCategory}
                  onChange={(event) => setSelectedCategory(event.target.value)}
                  className="rounded-full border border-[#d9dfcf] bg-white px-5 py-3 text-sm text-[#384131] outline-none transition focus:border-[#2f9c44]"
                >
                  <option value="">Todas</option>
                  <option value="Verduras">Verduras</option>
                  <option value="Legumes">Legumes</option>
                  <option value="Cogumelos">Cogumelos</option>
                  <option value="Cestas">Cestas</option>
                </select>
              </div>
            </div>

            <div className="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
              {filteredProducts.map((product) => (
                <article key={product.id} className="overflow-hidden rounded-[30px] border border-[#d9dfcf] bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                  <div className="relative">
                    <img src={product.image} alt={product.name} className="h-64 w-full object-cover" />
                    <span className="absolute left-4 top-4 rounded-full bg-[#f6ecd2] px-3 py-1 text-xs font-black text-[#8f5b11] shadow-sm">
                      {product.badge}
                    </span>
                  </div>
                  <div className="p-5">
                    <p className="text-sm font-bold uppercase tracking-[0.16em] text-[#2f9c44]">{product.category}</p>
                    <h3 className="mt-2 text-2xl font-black text-[#2f4b1f]">{product.name}</h3>
                    <p className="mt-3 text-sm leading-6 text-[#64705a]">{product.description}</p>
                    <div className="mt-4 flex items-end gap-2">
                      <span className="text-2xl font-black text-[#3d2d13]">{formatPrice(product.price)}</span>
                      <span className="pb-1 text-sm text-[#717a66]">/ {product.unit}</span>
                    </div>
                    <button onClick={() => addToCart(product)} className="mt-5 w-full rounded-full bg-[#2f9c44] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#267c37]">
                      Adicionar ao carrinho
                    </button>
                  </div>
                </article>
              ))}
            </div>
          </div>
        </section>

        <section id="como-funciona" className="mx-auto max-w-7xl px-6 py-16 lg:px-8">
          <SectionTitle
            eyebrow="Como funciona"
            title="Fluxo completo da landing page até o pedido"
            description="A página foi estruturada para reduzir atrito e permitir evolução para operação real de ecommerce local."
          />

          <div className="mt-10 grid gap-6 lg:grid-cols-4">
            {[
              { step: '01', title: 'Descubra os produtos', text: 'O visitante entra pela landing page, entende a proposta da marca e navega pelas categorias.' },
              { step: '02', title: 'Adicione ao carrinho', text: 'Os produtos podem ser adicionados diretamente dos cards, sem sair da vitrine principal.' },
              { step: '03', title: 'Revise o pedido', text: 'O carrinho lateral concentra itens, quantidades, subtotal, frete e CTA de checkout.' },
              { step: '04', title: 'Finalize a compra', text: 'No checkout, o cliente informa dados de entrega e seleciona a forma de pagamento.' },
            ].map((item) => (
              <div key={item.step} className="rounded-[30px] border border-[#d9dfcf] bg-white p-6 shadow-sm">
                <p className="text-sm font-black tracking-[0.24em] text-[#9f6a1d]">{item.step}</p>
                <h3 className="mt-3 text-2xl font-black text-[#2f4b1f]">{item.title}</h3>
                <p className="mt-4 text-sm leading-7 text-[#5f6b54]">{item.text}</p>
              </div>
            ))}
          </div>
        </section>

        <section className="bg-[#2f4b1f] py-16 text-white">
          <div className="mx-auto grid max-w-7xl gap-8 px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
            <div>
              <SectionTitle
                eyebrow="Estrutura comercial"
                title="Uma base pronta para evoluir para ecommerce real"
                description="O projeto já inclui áreas estratégicas para marketing, catálogo, prova social, carrinho e checkout."
              />
              <div className="mt-8 grid gap-4 sm:grid-cols-3">
                {benefits.map((benefit) => (
                  <div key={benefit.title} className="rounded-3xl border border-white/10 bg-white/5 p-5">
                    <h3 className="text-lg font-black text-[#f3d36b]">{benefit.title}</h3>
                    <p className="mt-3 text-sm leading-7 text-[#dbe4d5]">{benefit.description}</p>
                  </div>
                ))}
              </div>
            </div>

            <div className="rounded-[34px] border border-[#d8c48a] bg-[linear-gradient(180deg,#fff7df_0%,#f6f0d8_100%)] p-8 shadow-xl lg:p-10">
              <p className="text-sm font-black uppercase tracking-[0.2em] text-[#9f6a1d]">Oferta destacada</p>
              <h3 className="mt-3 text-3xl font-black tracking-tight text-[#624615]">
                Frete grátis acima de {formatPrice(80)}
              </h3>
              <div className="mt-6 space-y-4 text-base leading-8 text-[#6a5a36]">
                <p>• Incentiva ticket médio maior.</p>
                <p>• Regra já refletida automaticamente no carrinho.</p>
                <p>• Pode ser alterada para campanhas sazonais.</p>
                <p>• Excelente para cestas premium e pedidos recorrentes.</p>
              </div>
              <a href="#produtos" className="mt-8 inline-block rounded-full bg-[#9f6a1d] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#7f5518]">
                Aproveitar oferta
              </a>
            </div>
          </div>
        </section>

        <section id="depoimentos" className="mx-auto max-w-7xl px-6 py-16 lg:px-8">
          <SectionTitle
            eyebrow="Depoimentos"
            title="Bloco de prova social para aumentar confiança"
            description="Esses cards ajudam a validar a qualidade percebida da marca e apoiam a conversão da landing page."
          />

          <div className="mt-10 grid gap-6 lg:grid-cols-3">
            {testimonials.map((item) => (
              <div key={item.name} className="rounded-[30px] border border-[#d9dfcf] bg-white p-6 shadow-sm">
                <p className="text-base leading-8 text-[#54604a]">“{item.text}”</p>
                <div className="mt-6 border-t border-[#edf1e8] pt-4">
                  <p className="font-black text-[#2f4b1f]">{item.name}</p>
                  <p className="text-sm text-[#6f775f]">{item.role}</p>
                </div>
              </div>
            ))}
          </div>
        </section>

        <section className="border-t border-[#d9dfcf] bg-white/80 py-16">
          <div className="mx-auto max-w-7xl px-6 lg:px-8">
            <div className="rounded-[36px] border border-[#d9dfcf] bg-[linear-gradient(135deg,#eef7e7_0%,#f9f6e8_100%)] p-8 lg:flex lg:items-center lg:justify-between lg:p-10">
              <div>
                <p className="text-sm font-black uppercase tracking-[0.22em] text-[#9f6a1d]">Chamada final</p>
                <h3 className="mt-3 max-w-2xl text-3xl font-black tracking-tight text-[#2f4b1f] sm:text-4xl">
                  Transforme visitantes em pedidos com uma página pronta para vender.
                </h3>
                <p className="mt-4 max-w-2xl text-base leading-7 text-[#5f6b54]">
                  Use esta base como landing page comercial, catálogo institucional ou loja completa integrada com seu back-end.
                </p>
              </div>
              <div className="mt-6 flex flex-col gap-3 lg:mt-0">
                <a href="#produtos" className="rounded-full bg-[#2f9c44] px-7 py-3 text-center text-sm font-bold text-white transition hover:bg-[#267c37]">
                  Ver catálogo
                </a>
                <button onClick={() => setCheckoutOpen(true)} className="rounded-full border border-[#9f6a1d] bg-white px-7 py-3 text-sm font-bold text-[#7c5316] transition hover:bg-[#f8f0da]">
                  Ir para checkout
                </button>
              </div>
            </div>
          </div>
        </section>
      </main>

      <footer id="contato" className="border-t border-[#d9dfcf] bg-[#f1f5ea]">
        <div className="mx-auto grid max-w-7xl gap-10 px-6 py-12 lg:grid-cols-[1.1fr_0.9fr_0.9fr] lg:px-8">
          <div>
            <div className="flex items-center gap-4">
              <div className="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border border-[#d8c48a] bg-white shadow-sm">
                <img src={brand.logo} alt={brand.name} className="h-full w-full object-contain p-1" />
              </div>
              <div>
                <p className="text-2xl font-black text-[#2f9c44]">{brand.name}</p>
                <p className="text-sm text-[#66715b]">{brand.slogan}</p>
              </div>
            </div>
            <p className="mt-5 max-w-xl text-sm leading-7 text-[#5f6b54]">
              Layout completo com landing page, vitrine de produtos, carrinho lateral e checkout pronto para integração real com Laravel, Vue, React, API ou gateway de pagamento.
            </p>
          </div>

          <div>
            <p className="text-sm font-black uppercase tracking-[0.2em] text-[#9f6a1d]">Mapa do site</p>
            <div className="mt-4 space-y-3 text-sm font-semibold text-[#55614a]">
              <a href="#inicio" className="block transition hover:text-[#2f9c44]">Início</a>
              <a href="#categorias" className="block transition hover:text-[#2f9c44]">Categorias</a>
              <a href="#produtos" className="block transition hover:text-[#2f9c44]">Loja</a>
              <a href="#como-funciona" className="block transition hover:text-[#2f9c44]">Como funciona</a>
              <a href="#depoimentos" className="block transition hover:text-[#2f9c44]">Depoimentos</a>
            </div>
          </div>

          <div>
            <p className="text-sm font-black uppercase tracking-[0.2em] text-[#9f6a1d]">Contato</p>
            <div className="mt-4 space-y-3 text-sm text-[#55614a]">
              <p>{brand.whatsapp}</p>
              <p>{brand.email}</p>
              <p>{brand.address}</p>
              <p>Seg a sáb • 7h às 18h</p>
            </div>
          </div>
        </div>
      </footer>

      {cartOpen ? (
        <div className="fixed inset-0 z-50 flex justify-end bg-black/35">
          <div className="flex h-full w-full max-w-xl flex-col bg-[#fcfdf9] shadow-2xl">
            <div className="flex items-center justify-between border-b border-[#d9dfcf] px-6 py-5">
              <div>
                <h3 className="text-2xl font-black text-[#2f4b1f]">Seu carrinho</h3>
                <p className="text-sm text-[#6f775f]">{cartCount} item(ns) selecionado(s)</p>
              </div>
              <button onClick={() => setCartOpen(false)} className="rounded-full border border-[#d9dfcf] px-4 py-2 text-sm font-bold text-[#55614a]">
                Fechar
              </button>
            </div>

            <div className="flex-1 overflow-y-auto px-6 py-5">
              {cart.length === 0 ? (
                <div className="rounded-[28px] border border-dashed border-[#d0d8c6] bg-white p-8 text-center">
                  <p className="text-xl font-black text-[#2f4b1f]">Carrinho vazio</p>
                  <p className="mt-3 text-sm leading-7 text-[#64705a]">Adicione produtos para visualizar subtotal, frete e finalizar sua compra.</p>
                </div>
              ) : (
                <div className="space-y-4">
                  {cart.map((item) => (
                    <div key={item.product.id} className="rounded-[28px] border border-[#d9dfcf] bg-white p-4 shadow-sm">
                      <div className="flex gap-4">
                        <img src={item.product.image} alt={item.product.name} className="h-24 w-24 rounded-2xl object-cover" />
                        <div className="flex-1">
                          <div className="flex items-start justify-between gap-3">
                            <div>
                              <p className="text-sm font-bold uppercase tracking-[0.16em] text-[#2f9c44]">{item.product.category}</p>
                              <h4 className="mt-1 text-lg font-black text-[#2f4b1f]">{item.product.name}</h4>
                              <p className="mt-1 text-sm text-[#6f775f]">{formatPrice(item.product.price)} / {item.product.unit}</p>
                            </div>
                            <button onClick={() => removeItem(item.product.id)} className="text-sm font-bold text-[#9f6a1d]">
                              Remover
                            </button>
                          </div>
                          <div className="mt-4 flex items-center justify-between gap-4">
                            <QuantityControl
                              quantity={item.quantity}
                              onDecrease={() => updateQuantity(item.product.id, -1)}
                              onIncrease={() => updateQuantity(item.product.id, 1)}
                            />
                            <p className="text-lg font-black text-[#3d2d13]">{formatPrice(item.product.price * item.quantity)}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>

            <div className="border-t border-[#d9dfcf] bg-white px-6 py-5">
              <div className="space-y-3 text-sm">
                <div className="flex items-center justify-between text-[#55614a]">
                  <span>Subtotal</span>
                  <span className="font-bold">{formatPrice(subtotal)}</span>
                </div>
                <div className="flex items-center justify-between text-[#55614a]">
                  <span>Frete</span>
                  <span className="font-bold">{shipping === 0 ? 'Grátis' : formatPrice(shipping)}</span>
                </div>
                <div className="flex items-center justify-between border-t border-[#edf1e8] pt-3 text-base font-black text-[#2f4b1f]">
                  <span>Total</span>
                  <span>{formatPrice(total)}</span>
                </div>
              </div>
              <button
                onClick={openCheckout}
                className="mt-5 w-full rounded-full bg-[#2f9c44] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#267c37]"
              >
                Ir para checkout
              </button>
            </div>
          </div>
        </div>
      ) : null}

      {checkoutOpen ? (
        <div className="fixed inset-0 z-[60] overflow-y-auto bg-black/45 px-4 py-8">
          <div className="mx-auto max-w-5xl rounded-[36px] border border-[#d8c48a] bg-[#fcfdf9] shadow-2xl">
            <div className="flex items-center justify-between border-b border-[#d9dfcf] px-6 py-5 lg:px-8">
              <div>
                <h3 className="text-2xl font-black text-[#2f4b1f]">Checkout</h3>
                <p className="text-sm text-[#6f775f]">Revise seus dados e finalize o pedido</p>
              </div>
              <button onClick={() => setCheckoutOpen(false)} className="rounded-full border border-[#d9dfcf] px-4 py-2 text-sm font-bold text-[#55614a]">
                Fechar
              </button>
            </div>

            <div className="grid gap-8 px-6 py-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-8">
              <div>
                <div className="grid gap-4 sm:grid-cols-2">
                  <label className="block">
                    <span className="mb-2 block text-sm font-bold text-[#2f4b1f]">Nome completo</span>
                    <input
                      value={checkoutForm.name}
                      onChange={(event) => setCheckoutForm((current) => ({ ...current, name: event.target.value }))}
                      className="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
                    />
                  </label>
                  <label className="block">
                    <span className="mb-2 block text-sm font-bold text-[#2f4b1f]">Telefone</span>
                    <input
                      value={checkoutForm.phone}
                      onChange={(event) => setCheckoutForm((current) => ({ ...current, phone: event.target.value }))}
                      className="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
                    />
                  </label>
                  <label className="block sm:col-span-2">
                    <span className="mb-2 block text-sm font-bold text-[#2f4b1f]">E-mail</span>
                    <input
                      value={checkoutForm.email}
                      onChange={(event) => setCheckoutForm((current) => ({ ...current, email: event.target.value }))}
                      className="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
                    />
                  </label>
                  <label className="block sm:col-span-2">
                    <span className="mb-2 block text-sm font-bold text-[#2f4b1f]">Endereço</span>
                    <input
                      value={checkoutForm.address}
                      onChange={(event) => setCheckoutForm((current) => ({ ...current, address: event.target.value }))}
                      className="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
                    />
                  </label>
                  <label className="block">
                    <span className="mb-2 block text-sm font-bold text-[#2f4b1f]">Bairro</span>
                    <input
                      value={checkoutForm.neighborhood}
                      onChange={(event) => setCheckoutForm((current) => ({ ...current, neighborhood: event.target.value }))}
                      className="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
                    />
                  </label>
                  <label className="block">
                    <span className="mb-2 block text-sm font-bold text-[#2f4b1f]">Cidade</span>
                    <input
                      value={checkoutForm.city}
                      onChange={(event) => setCheckoutForm((current) => ({ ...current, city: event.target.value }))}
                      className="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
                    />
                  </label>
                  <label className="block sm:col-span-2">
                    <span className="mb-2 block text-sm font-bold text-[#2f4b1f]">Forma de pagamento</span>
                    <select
                      value={checkoutForm.paymentMethod}
                      onChange={(event) => setCheckoutForm((current) => ({ ...current, paymentMethod: event.target.value }))}
                      className="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
                    >
                      <option>Pix</option>
                      <option>Cartão na entrega</option>
                      <option>Dinheiro</option>
                    </select>
                  </label>
                  <label className="block sm:col-span-2">
                    <span className="mb-2 block text-sm font-bold text-[#2f4b1f]">Observações</span>
                    <textarea
                      value={checkoutForm.notes}
                      onChange={(event) => setCheckoutForm((current) => ({ ...current, notes: event.target.value }))}
                      rows={4}
                      className="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
                    />
                  </label>
                </div>

                {checkoutMessage ? (
                  <div className={`mt-5 rounded-2xl border px-4 py-3 text-sm ${orderPlaced ? 'border-[#cfe8d5] bg-[#eef8f0] text-[#246b34]' : 'border-[#eed8b0] bg-[#fff7e7] text-[#7f5518]'}`}>
                    {checkoutMessage}
                  </div>
                ) : null}
              </div>

              <div>
                <div className="rounded-[30px] border border-[#d9dfcf] bg-white p-6 shadow-sm">
                  <h4 className="text-xl font-black text-[#2f4b1f]">Resumo do pedido</h4>
                  <div className="mt-5 space-y-4">
                    {cart.length === 0 ? (
                      <p className="text-sm text-[#6f775f]">Nenhum item no carrinho.</p>
                    ) : (
                      cart.map((item) => (
                        <div key={item.product.id} className="flex items-center justify-between gap-4 border-b border-[#edf1e8] pb-4">
                          <div>
                            <p className="font-bold text-[#2f4b1f]">{item.product.name}</p>
                            <p className="text-sm text-[#6f775f]">{item.quantity}x • {formatPrice(item.product.price)}</p>
                          </div>
                          <p className="font-black text-[#3d2d13]">{formatPrice(item.product.price * item.quantity)}</p>
                        </div>
                      ))
                    )}
                  </div>

                  <div className="mt-6 space-y-3 text-sm">
                    <div className="flex items-center justify-between text-[#55614a]">
                      <span>Subtotal</span>
                      <span className="font-bold">{formatPrice(subtotal)}</span>
                    </div>
                    <div className="flex items-center justify-between text-[#55614a]">
                      <span>Frete</span>
                      <span className="font-bold">{shipping === 0 ? 'Grátis' : formatPrice(shipping)}</span>
                    </div>
                    <div className="flex items-center justify-between border-t border-[#edf1e8] pt-3 text-base font-black text-[#2f4b1f]">
                      <span>Total</span>
                      <span>{formatPrice(total)}</span>
                    </div>
                  </div>

                  <button onClick={submitCheckout} className="mt-6 w-full rounded-full bg-[#2f9c44] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#267c37]">
                    Finalizar pedido
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      ) : null}
    </div>
  )
}
