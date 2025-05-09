import { useCart } from './CartContext';

export default function CartOverlay({ onClose }: { onClose: () => void }) {
  const { cart, updateQuantity, clearCart } = useCart();
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
  const totalLabel = totalItems === 1 ? '1 Item' : `${totalItems} Items`;
  const totalPrice = cart.reduce((sum, item) => sum + (item.price || 0) * item.quantity, 0);

  // TODO: Implement GraphQL mutation for placing order

  return (
    <div className="cart-overlay">
      <div className="cart-content">
        <h2>Cart</h2>
        <div>{totalLabel}</div>
        <ul>
          {cart.map(item => (
            <li key={item.productId + JSON.stringify(item.options)}>
              <img src={item.image} alt={item.name} width={50} />
              <div>{item.name}</div>
              <div>
                {Object.entries(item.options).map(([k, v]) => (
                  <span key={k}>{k}: {v} </span>
                ))}
                {/* Show other available options here */}
              </div>
              <div>
                <button onClick={() => updateQuantity(item, 1)}>+</button>
                <span>{item.quantity}</span>
                <button onClick={() => updateQuantity(item, -1)}>-</button>
              </div>
            </li>
          ))}
        </ul>
        <div>Total: {totalPrice}</div>
        <button
          disabled={cart.length === 0}
          onClick={() => {
            // Call GraphQL mutation to place order
            clearCart();
            onClose();
          }}
        >
          Place order
        </button>
        <button onClick={onClose}>Close</button>
      </div>
      <div className="cart-backdrop" onClick={onClose} />
    </div>
  );
}