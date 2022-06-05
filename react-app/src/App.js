import './App.css';
import {Route, BrowserRouter, Routes} from "react-router-dom";
import SignIn from "./SignIn";
import GoogleCallback from "./GoogleCallback";

function App() {
  return (
      <BrowserRouter>
          <Routes>
              <Route path="/" element={<SignIn />}></Route>
              <Route path="/auth/google" element={<GoogleCallback />}></Route>
          </Routes>
      </BrowserRouter>
  );
}

export default App;
