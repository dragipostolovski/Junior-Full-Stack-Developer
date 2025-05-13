import ReactDOM from 'react-dom/client';
import { ApolloClient, InMemoryCache, ApolloProvider } from '@apollo/client';
import App from './App';
import { CartProvider } from './components/CartContext';
import { CurrencyProvider } from './components/CurrencyContext';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import ProductDetails from './components/ProductDetails';

const client = new ApolloClient({
  uri: 'https://projectsengine.lovestoblog.com/backend', // Your backend GraphQL endpoint
  cache: new InMemoryCache(),
});

ReactDOM.createRoot(document.getElementById('root')!).render(
  <ApolloProvider client={client}>
    <CurrencyProvider>
      <CartProvider>
        <BrowserRouter>
          <Routes>
            <Route path="/" element={<App />} />
            <Route path="/product/:id" element={<ProductDetails />} />
          </Routes>
        </BrowserRouter>
      </CartProvider>
    </CurrencyProvider>
  </ApolloProvider>
);