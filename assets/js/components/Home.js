// ./assets/js/components/Home.js

import React, {Component} from 'react';
import {Route, Redirect, Switch, Link} from 'react-router-dom';
import SetupCheck from "./SetupCheck";
import CurrencyRates from './CurrencyRates';

class Home extends Component {

    render() {
        return (
            <div className='container'>
                <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
                    <Link className={"navbar-brand"} to={"#"}> Telemedi Zadanko </Link>
                    <div id="navbarText">
                        <ul className="navbar-nav mr-auto">
                            <li className="nav-item">
                                <Link className={"nav-link"} to={"/setup-check"}> React Setup Check </Link>
                            </li>

                        </ul>
                    </div>
                </nav>
                <div className="container p-1">
                    <CurrencyRates />
                </div>
                <Switch>
                    <Route path="/setup-check" component={SetupCheck} />
                </Switch>
            </div>
        )
    }
}

export default Home;
