import {getAssetPath} from "../utils/assetsUtils";
import '../../../styles/components/Header.scss';


export const getLogoUrl = () => {
    const filename = 'images/logo.png';
    return getAssetPath(filename);
}

//todo: replace img with build path
const Header = () => {
    return (
        <header>
            <div className="company">
                <a href="https://www.grotepodcastlas.nl/" target="_blank" rel="naar grotepodcastlas.nl gaan">
                    <img src="/build/images/logo.a907eb4ac8e7d37afadee2bf06a88d4f.png" alt="Logo"/>
                </a>
                <h1>Wereld Restaurants</h1>
            </div>
            <div className="buttons-wrapper">
                <a href="#" className="button">
                    Over ons
                </a>
            </div>
        </header>
    );
}

export default Header;
