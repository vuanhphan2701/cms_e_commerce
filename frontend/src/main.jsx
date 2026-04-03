import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import App from './App.jsx'
import './index.css'
import { AlertProvider } from './components/common/AlertContext.jsx'
import AlertMessage from './components/common/AlertMessage.jsx'


createRoot(document.getElementById('root')).render(
  <AlertProvider>
    <StrictMode>
      <App />
      <AlertMessage />
    </StrictMode>
  </AlertProvider>

);
