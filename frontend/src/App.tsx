import { gql, useQuery } from '@apollo/client';
import Header from './components/Header';
import { useCart } from './components/CartContext';
import { useCurrency } from './components/CurrencyContext';
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';

const PRODUCTS_QUERY = gql`
  query {
    products {
      id
      name
      category
      inStock
      stock
      gallery
      prices {
        amount
        currency {
          label
          symbol
        }
      }
    }
  }
`;

function toKebab(str: string) {
  return str.replace(/\s+/g, '-').toLowerCase();
}

function App() {
  const { loading, error, data } = useQuery(PRODUCTS_QUERY);
  const { addToCart, cart } = useCart();
  const { currency } = useCurrency();
  const [activeCategory, setActiveCategory] = useState<string | null>(null);
  const [hoveredProduct, setHoveredProduct] = useState<string | null>(null);
  const navigate = useNavigate();

  if (loading) return <p>Loading...</p>;
  if (error) return <p>Error: {error.message}</p>;
  if (!data) return <p>No data found.</p>; // <-- Add this line for safety

 // Get unique categories
 const categories = Array.from(new Set(data.products.map((p: any) => p.category))) as string[];

 // Filter products by category
 const filteredProducts = activeCategory
   ? data.products.filter((p: any) => p.category === activeCategory)
   : data.products;

  return (
    <>
      <Header />
      <nav>
        {categories.map((cat: string) => (
          <button
            key={cat}
            data-testid={activeCategory === cat ? 'active-category-link' : 'category-link'}
            style={{ fontWeight: activeCategory === cat ? 'bold' : 'normal' }}
            onClick={() => setActiveCategory(cat)}
          >
            {cat}
          </button>
        ))}
        <button
          data-testid={activeCategory === null ? 'active-category-link' : 'category-link'}
          onClick={() => setActiveCategory(null)}
        >
          All
        </button>
      </nav>
      <div>
        <h1>Products</h1>

        <ul style={{ display: 'flex', flexWrap: 'wrap', gap: '1rem', listStyle: 'none', padding: 0 }}>
          {filteredProducts.map((product: any) => {
            // Check how many of this product are in the cart
            const cartCount = cart
              ? cart.filter((item: any) => item.productId === product.id).reduce((sum: number, item: any) => sum + (item.quantity || 1), 0)
              : 0;
            // Only allow adding if cartCount < stock
            const canAddToCart = product.stock > 0 && cartCount < product.stock;
            // Find the price object for the selected currency
            const priceObj = product.prices.find((p: any) => p.currency.label === currency.label) || product.prices[0];
            return (
              <li
                key={product.id}
                data-testid={`product-${toKebab(product.name)}`}
                style={{
                  opacity: product.stock > 0 ? 1 : 0.5,
                  pointerEvents: product.stock > 0 ? 'auto' : 'none',
                  position: 'relative',
                  width: 220,
                  border: '1px solid #eee',
                  padding: 10,
                  borderRadius: 8,
                  background: '#fff'
                }}
                onMouseEnter={() => setHoveredProduct(product.id)}
                onMouseLeave={() => setHoveredProduct(null)}
                onClick={() => navigate(`/product/${product.id}`)}
              >
                <div style={{ position: 'relative' }}>
                  <img
                    src={product.gallery && product.gallery.length > 0 ? product.gallery[0] : ''}
                    alt={product.name}
                    style={{
                      width: 200,
                      height: 200,
                      objectFit: 'cover',
                      filter: product.stock > 0 ? 'none' : 'grayscale(100%)'
                    }}
                    data-testid={`product-image-${product.id}`}
                  />
                  {product.stock === 0 && (
                    <div
                      style={{
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        width: '100%',
                        height: '100%',
                        background: 'rgba(255,255,255,0.7)',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        color: 'red',
                        fontWeight: 'bold'
                      }}
                    >
                      Out of Stock
                    </div>
                  )}
                  {/* Quick Shop button (only for in-stock, only on hover) */}
                  {product.stock > 0 && hoveredProduct === product.id && (
                    <button
                      data-testid={`quick-shop-${product.id}`}
                      style={{
                        position: 'absolute',
                        bottom: 10,
                        right: 10,
                        background: 'green',
                        color: 'white',
                        border: 'none',
                        borderRadius: '50%',
                        width: 40,
                        height: 40,
                        fontSize: 20,
                        cursor: canAddToCart ? 'pointer' : 'not-allowed',
                        opacity: canAddToCart ? 1 : 0.5
                      }}
                      disabled={!canAddToCart}
                      onClick={e => {
                        e.stopPropagation();
                        if (canAddToCart) {
                          addToCart({
                            productId: product.id,
                            name: product.name,
                            image: product.gallery && product.gallery.length > 0 ? product.gallery[0] : '',
                            price: priceObj.amount, // <-- use selected currency price
                            options: {},
                            availableOptions: {}
                          });
                        }
                      }}
                    >
                      🛒
                    </button>
                  )}
                </div>
                <div>
                  {product.name} ({product.category})
                </div>
                <div>
                  {product.prices && product.prices.length > 0
                    ? `${priceObj.currency.symbol}${priceObj.amount.toFixed(2)}`
                    : '$0.00'}
                </div>
              </li>
            );
          })}
        </ul>

      </div>
    </>
  );
}

export default App;