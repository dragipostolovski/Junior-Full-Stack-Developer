import { gql, useQuery } from '@apollo/client';
import { useParams } from 'react-router-dom';
import { useCart } from './CartContext';
import { useState } from 'react';
import { useCurrency } from './CurrencyContext';


const PRODUCT_QUERY = gql`
  query Product($id: String!) {
    product(id: $id) {
      id
      name
      description
      inStock
      gallery
      attributes {
        id
        name
        type
        items {
          id
          displayValue
          value
        }
      }
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

export default function ProductDetails() {
  const { id } = useParams<{ id: string }>();
  const { loading, error, data } = useQuery(PRODUCT_QUERY, { variables: { id } });
  const { addToCart } = useCart();
  const [selectedOptions, setSelectedOptions] = useState<Record<string, string>>({});
  const { currency } = useCurrency();
  if (loading) return <p>Loading...</p>;
  if (error) return <p>Error: {error.message}</p>;
  const product = data.product;

  const handleSelect = (attrId: string, value: string) => {
    setSelectedOptions(prev => ({ ...prev, [attrId]: value }));
  };

  const canAddToCart = product.inStock && product.attributes.every(
    (attr: any) => selectedOptions[attr.id]
  );

  const priceObj = product.prices.find((p: any) => p.currency.label === currency.label) || product.prices[0];

  return (
    <div>
      <h1>{product.name}</h1>
      <img src={product.gallery[0]} alt={product.name} width={300} />
      <div dangerouslySetInnerHTML={{ __html: product.description }} />
      {product.attributes.map((attr: any) => (
        <div key={attr.id}>
          <div>{attr.name}:</div>
          {attr.items.map((item: any) => (
            <button
              key={item.id}
              style={{
                fontWeight: selectedOptions[attr.id] === item.id ? 'bold' : 'normal'
              }}
              onClick={() => handleSelect(attr.id, item.id)}
            >
              {item.displayValue}
            </button>
          ))}
        </div>
      ))}
        <div>
        Price: {priceObj.currency.symbol}{priceObj.amount}
      </div>
      <button
        disabled={!canAddToCart}
        onClick={() => addToCart({
          productId: product.id,
          name: product.name,
          image: product.gallery[0],
          price: priceObj.amount,
          options: selectedOptions,
          availableOptions: product.attributes.reduce((acc: any, attr: any) => {
            acc[attr.id] = attr.items.map((i: any) => i.id);
            return acc;
          }, {})
        })}
      >
        Add to Cart
      </button>
    </div>
  );
}