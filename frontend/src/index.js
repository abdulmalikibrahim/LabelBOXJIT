import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Index from './Pages/Index';
import Print from './Pages/Print';

const root = ReactDOM.createRoot(document.getElementById('root'));
const API_URL = process.env.REACT_APP_API_URL;

const App = () => {
  return(
    <Router>
      <Routes>
        <Route path='/' element={<Index api_url={API_URL} />}/>
        <Route path='/printKanban' element={<Print api_url={API_URL} typeDownload={"kanban"} />}/>
      </Routes>
    </Router>
  )
}

root.render(
  <App />
);
