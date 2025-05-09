import { useCart } from './CartContext';
import { useState } from 'react';
import CartOverlay from './CartOverlay';
import { useCurrency } from "./CurrencyContext";


export default function Header() {
  const { cart } = useCart();
  const [open, setOpen] = useState(false);
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
  const { currency, setCurrency } = useCurrency();

  return (
    <header>
      {/* ...other header content... */}
      <button data-testid="cart-btn" onClick={() => setOpen(o => !o)}>
        Cart
        {totalItems > 0 && <span className="cart-bubble">{totalItems}</span>}
      </button>
      {open && <CartOverlay onClose={() => setOpen(false)} />}
      <select
        value={currency.label}
        onChange={e => {
          const selected = e.target.value;
          if (selected === "USD") setCurrency({ label: "USD", symbol: "$" });
          if (selected === "EUR") setCurrency({ label: "EUR", symbol: "€" });
          // Add more as needed
        }}
      >
        <option value="USD">$ USD</option>
        <option value="EUR">€ EUR</option>
      </select>
    </header>
  );
}