class NavLink extends Component
{
    public $active;

    public function __construct($active = false)
    {
        $this->active = $active;
    }

    public function render()
    {
        return view('components.nav-link');
    }
}